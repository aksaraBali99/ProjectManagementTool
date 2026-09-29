<?php

namespace App\Services;

use App\Enums\DocumentAccessLevel;
use App\Enums\FileCategory;
use App\Exceptions\FileStorageException;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Throwable;

/**
 * task #73 phase 1: the ONE place an actually-uploaded file becomes a
 * Document row — used by both new Phase 1 endpoints (the Documents page's
 * own "Upload file" option, and the Task edit page's inline form) AND
 * RichTextDocumentController::store() (the editor's attach-document
 * button, refactored onto this same service), reconciling what used to be
 * two independently-evolving creation paths into one. Not used by
 * TaskManagementController::attachDocumentChips() — that path has no
 * UploadedFile at all by the time it runs (the bytes were already moved
 * into place at task-save time; see that method's own docblock) — but it
 * still calls recordUploadAudit() below, so every path that results in a
 * real stored file gets the identical audit shape.
 *
 * Deletes the just-stored file again if creating the Document row fails
 * for any reason, so an upload can never leave an orphaned file with no
 * record pointing at it.
 */
class DocumentUploadService
{
    /**
     * task #73: a Document upload isn't restricted to the "document"
     * category (PDF/Word/Excel/PowerPoint/text/CSV) alone — it can also
     * be an image, audio, or video file, each validated against ITS OWN
     * category's size/type limits (config/filestorage.php), not a single
     * flat limit for everything. Checked in this order — document first,
     * since that's still the common case and it's a harmless tie-break
     * for any file (none, in practice) whose extension could plausibly
     * appear in more than one category's allow-list.
     *
     * @var list<FileCategory>
     */
    private const UPLOAD_CATEGORIES = [FileCategory::Document, FileCategory::Image, FileCategory::Audio, FileCategory::Video];

    public function __construct(private readonly FileStorageService $storage) {}

    /**
     * task #73: resolves which of self::UPLOAD_CATEGORIES this file
     * actually is, once, so both uploadForOrganization() and
     * uploadForTask() below apply the SAME detection — a file that
     * matches none of them (a genuinely unsupported type) fails with a
     * clear message rather than being silently forced into the document
     * category and rejected there instead.
     *
     * @throws FileStorageException the file's type matches none of the accepted categories
     */
    private function resolveCategory(UploadedFile $file): FileCategory
    {
        $category = $this->storage->detectCategory($file, self::UPLOAD_CATEGORIES);

        if ($category === null) {
            throw FileStorageException::noMatchingCategory($file->getMimeType() ?? $file->getClientOriginalExtension());
        }

        return $category;
    }

    /**
     * Documents page's own "Upload file" option — no task in scope at
     * all, filed under organizations/{organizationId}/... (see
     * FileStorageService::uploadForOrganization()). `link` is left null:
     * per the schema note this phase introduces, an uploaded file's URL
     * is only ever the computed one (Document::url()), never a stored
     * `link` value — that column stays reserved for genuinely external
     * links and the editor's own chip-resolution needs (see
     * uploadForTask()'s own $populateLegacyLink).
     *
     * task #73 phase 2: $folderId is the current folder on the Documents
     * page (null = company root) — the caller (DocumentController::store())
     * has already validated it belongs to $organizationId before this is
     * ever reached, since that check needs a database lookup this service
     * has no reason to duplicate.
     *
     * @throws FileStorageException file fails validation or the disk write itself fails
     */
    public function uploadForOrganization(
        UploadedFile $file,
        int $organizationId,
        DocumentAccessLevel $accessLevel,
        User $uploader,
        ?string $name = null,
        ?int $folderId = null,
    ): Document {
        $stored = $this->storage->uploadForOrganization($file, $this->resolveCategory($file), $organizationId);

        return $this->createRecord($file, $stored, $organizationId, $accessLevel, $uploader, $name, null, populateLegacyLink: false, folderId: $folderId);
    }

    /**
     * Both the Task edit page's own inline "Upload file" option
     * ($populateLegacyLink: false — same reasoning as
     * uploadForOrganization() above) and RichTextDocumentController::
     * store() (the editor's attach-document button, $populateLegacyLink:
     * true) go through here — the only difference between the two is
     * whether `link` also gets the computed URL: a file-chip embedded in
     * saved rich text has no document_id to resolve by, only its saved
     * href (see DocumentController::download()'s own docblock), so an
     * editor-created document MUST keep a real `link` value the chip's
     * exact href can still match — the new inline-upload form has no
     * chip and no such requirement.
     *
     * @throws FileStorageException file fails validation or the disk write itself fails
     */
    public function uploadForTask(
        UploadedFile $file,
        Task $task,
        DocumentAccessLevel $accessLevel,
        User $uploader,
        ?string $name = null,
        bool $populateLegacyLink = false,
    ): Document {
        $stored = $this->storage->upload($file, $this->resolveCategory($file), $task->id);

        // folderId always null — "Uploads from tasks, the editor, and
        // other paths go to the root" (task #73 phase 2).
        $document = $this->createRecord($file, $stored, $task->organization_id, $accessLevel, $uploader, $name, $task->id, $populateLegacyLink, folderId: null);

        app(TaskDocumentLinker::class)->attach($task, $document);

        return $document;
    }

    /**
     * Shared by both upload methods above: creates the Document row from
     * an already-stored file, computing sha256/size/mime/original
     * filename directly from the still-available UploadedFile (its temp
     * path is still on this server's local disk at this point, so
     * hashing it costs nothing extra — no re-download from R2 needed,
     * unlike attachDocumentChips()'s reconciliation case where the
     * UploadedFile itself is long gone by the time that code runs).
     * Deletes the file again if the row can't be created, so a failed
     * upload never leaves a stored file with nothing pointing at it.
     */
    private function createRecord(
        UploadedFile $file,
        StoredFile $stored,
        int $organizationId,
        DocumentAccessLevel $accessLevel,
        User $uploader,
        ?string $name,
        ?int $originTaskId,
        bool $populateLegacyLink,
        ?int $folderId = null,
    ): Document {
        try {
            $document = Document::create([
                'organization_id' => $organizationId,
                'uploaded_by' => $uploader->id,
                'name' => $name !== null && $name !== '' ? $name : $file->getClientOriginalName(),
                'link' => $populateLegacyLink ? $stored->url : null,
                'storage_key' => $stored->path,
                'sha256_hash' => hash_file('sha256', $file->getRealPath()),
                'size_bytes' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'original_filename' => $file->getClientOriginalName(),
                'origin_task_id' => $originTaskId,
                'folder_id' => $folderId,
                // NOT re-resolved through resolveAccessLevel() here — the
                // editor's attach-document button (uploadForTask() via
                // RichTextDocumentController) has never offered a
                // choosable access level at all (it's hardcoded to
                // Internal for every role, unrelated to this phase); only
                // a caller that actually exposes the dropdown
                // (DocumentController::store()) resolves the access level
                // before calling in here.
                'access_level' => $accessLevel,
            ]);
        } catch (Throwable $e) {
            $this->storage->delete($stored->path);
            throw $e;
        }

        $this->recordUploadAudit($document);

        return $document;
    }

    /**
     * task #73 phase 1: "A Client-role user (in that company) never sees
     * the dropdown and is always saved as public, enforced server-side
     * even if the request says otherwise." Called by
     * DocumentController::store() — the only creation path that actually
     * exposes a chooseable access level to begin with — for BOTH its file
     * and link branches, before either ever reaches this service or
     * Document::create(). Deliberately NOT applied inside createRecord()
     * itself: the editor's attach-document button (uploadForTask() via
     * RichTextDocumentController) hardcodes Internal for every role
     * regardless of this phase, and forcing it there would incorrectly
     * override that unrelated, pre-existing behavior for a Client.
     */
    public function resolveAccessLevel(User $uploader, int $organizationId, DocumentAccessLevel $requested): DocumentAccessLevel
    {
        return $uploader->isClientInOrg($organizationId) ? DocumentAccessLevel::Public : $requested;
    }

    /**
     * document.uploaded — the one audit event this phase adds (Phase 2
     * adds the rest). Public and reused by attachDocumentChips() too
     * (that path creates its Document row directly, not through this
     * service, but still calls this so both ways a file ends up as a
     * genuine upload record produce the identical audit shape.
     */
    public function recordUploadAudit(Document $document): void
    {
        AuditLog::create([
            'organization_id' => $document->organization_id,
            'user_id' => $document->uploaded_by,
            'action' => 'document.uploaded',
            'entity_type' => 'document',
            'entity_id' => $document->id,
            'changes' => [
                'origin' => $document->origin_task_id === null ? 'documents_page' : 'task',
                'access_level' => $document->access_level->value,
                'size_bytes' => $document->size_bytes,
                'original_filename' => $document->original_filename,
            ],
        ]);
    }
}
