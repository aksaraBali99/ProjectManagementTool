<?php

namespace App\Http\Controllers;

use App\Enums\DocumentAccessLevel;
use App\Exceptions\DocumentAlreadyAttachedException;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Task;
use App\Policies\DocumentPolicy;
use App\Services\TaskDocumentLinker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TaskDocumentController extends Controller
{
    /**
     * task #73 phase 3: the "Attach existing" picker's list — same gate as
     * the button (TaskPolicy::attachDocuments()), documents restricted to
     * the task's own company (explicit, never trusted from the request —
     * DocumentPolicy::attachableInCompany() needs this since the tenant
     * scope is bypassed for super_admin/owner), Internal/Public only,
     * searched and paginated.
     *
     * Folder paths are computed from one query for every folder in the
     * company (not per document): $folderPaths below is a flat id => "A /
     * B / C" map built once, then a plain array lookup per row.
     */
    public function index(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('attachDocuments', $task);

        $data = $request->validate([
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $query = app(DocumentPolicy::class)->attachableInCompany(auth()->user(), $task->organization_id)
            ->with('uploader', 'folder');

        $search = trim((string) ($data['search'] ?? ''));
        if ($search !== '') {
            // Escape LIKE wildcards so a literal % or _ in the search term
            // is matched literally, not treated as a wildcard. LIKE is
            // case-insensitive by default for ASCII text on both MySQL's
            // and SQLite's default collations, so no LOWER()/UPPER()
            // wrapping is needed for that part of the requirement.
            //
            // whereRaw + an explicit ESCAPE clause, not the fluent
            // where(..., 'like', ...): MySQL's LIKE treats backslash as an
            // escape character by default, but SQLite's does NOT unless
            // one is named explicitly — without this, the escaped
            // backslashes above would search for literal backslashes on
            // SQLite instead of escaping the wildcard.
            //
            // The escape character itself is a BOUND parameter (`ESCAPE
            // ?`), not inlined into the SQL text as `ESCAPE '\'` — a
            // single backslash inside a quoted SQL string literal means
            // two different things on the two drivers this app runs on:
            // SQLite doesn't treat backslash as a string-literal escape
            // at all (so `'\'` is one backslash, as intended), but MySQL
            // does (so `'\'` is an unterminated string, a syntax error —
            // it needs `'\\'` in the raw SQL text for the parsed value to
            // be one backslash). Binding it as a parameter sidesteps that
            // entirely: PDO transmits the single-backslash value as data,
            // with no driver-specific string-literal quoting involved.
            // This shipped only ever tested against SQLite (this app's
            // test suite), so the MySQL-only syntax error wasn't caught
            // until a real search against the dev MySQL database failed.
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $likeValue = "%{$escaped}%";
            $query->where(function ($q) use ($likeValue) {
                $q->whereRaw('name LIKE ? ESCAPE ?', [$likeValue, '\\'])
                    ->orWhereRaw('original_filename LIKE ? ESCAPE ?', [$likeValue, '\\']);
            });
            // Relevance is "does it match at all" here, not ranked — with a
            // search term, newest-first would bury an older exact-ish
            // match under a pile of newer unrelated ones just as easily as
            // it'd surface one, so name order is left as the tiebreak
            // instead. Only the no-search-term case has an explicit
            // "newest first" requirement.
            $query->orderBy('name');
        } else {
            $query->orderByDesc('created_at');
        }

        $paginated = $query->paginate(25, ['*'], 'page', $data['page'] ?? 1);

        $folderPaths = $this->folderPaths($task->organization_id);
        $attachedIds = $task->documents()->pluck('documents.id');

        $paginated->getCollection()->transform(fn (Document $document) => [
            'id' => $document->id,
            'name' => $document->name,
            'original_filename' => $document->original_filename,
            'mime_type' => $document->mime_type,
            'size_bytes' => $document->size_bytes,
            'access_level' => $document->access_level->value,
            'uploader_name' => $document->uploader->name,
            'uploaded_at' => $document->created_at->toIso8601String(),
            'folder_path' => $document->folder_id !== null ? ($folderPaths[$document->folder_id] ?? null) : null,
            'already_attached' => $attachedIds->contains($document->id),
        ]);

        return response()->json($paginated);
    }

    /**
     * One query for every folder in the company, then an in-memory walk —
     * never one query per document row.
     *
     * @return array<int, string>
     */
    private function folderPaths(int $organizationId): array
    {
        $folders = DocumentFolder::where('organization_id', $organizationId)->get(['id', 'parent_id', 'name']);
        $byId = $folders->keyBy('id');

        $paths = [];
        foreach ($folders as $folder) {
            $segments = [];
            for ($cursor = $folder; $cursor !== null; $cursor = $cursor->parent_id !== null ? $byId->get($cursor->parent_id) : null) {
                array_unshift($segments, $cursor->name);
            }
            $paths[$folder->id] = implode(' / ', $segments);
        }

        return $paths;
    }

    /**
     * task #73 phase 3: the picker's attach action, and now the single
     * endpoint for attaching an existing document to a task — the old
     * <select>+button widget that used to post here (gated by full
     * task-edit rights) has been removed from the Task edit page rather
     * than kept as a second way in; see tasks/_documents.blade.php.
     *
     * Authorization is deliberately two independent checks, not one
     * combined policy method: TaskPolicy::attachDocuments() for "can this
     * user manage documents on this task at all" (the button/list gate),
     * and the real DocumentPolicy::view() — via Gate, never the picker's
     * own narrower attachableInCompany() query — for "can this user see
     * THIS specific document". A drift in the picker's own query could
     * then only ever hide a document it should have listed, never let an
     * unauthorized attach through.
     *
     * 404, not 403, for a document the viewer can't see or one in another
     * company — resolved and checked before anything about the document
     * is revealed. 422 for a private one (a real document, so this DOES
     * reveal existence, but with an actionable message instead of a
     * dead end) and for a repeat attach.
     */
    public function attach(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('attachDocuments', $task);

        $data = $request->validate([
            'document_id' => ['required', 'integer'],
        ]);

        // Document's own BelongsToOrganization scope already 404s a
        // cross-company document for everyone except super_admin/owner
        // (who bypass that scope) — the explicit organization_id check
        // below is what closes that gap for them specifically, since
        // "same company as the task" is a structural requirement for
        // attachability, independent of who's allowed to VIEW documents
        // in general.
        $document = Document::find($data['document_id']);

        if ($document === null
            || $document->organization_id !== $task->organization_id
            || ! Gate::allows('view', $document)) {
            abort(404);
        }

        if ($document->access_level === DocumentAccessLevel::Private) {
            abort(422, 'Private documents can\'t be attached to tasks. Change its access level first.');
        }

        $linker = app(TaskDocumentLinker::class);

        try {
            DB::transaction(function () use ($task, $document, $linker) {
                // Locked and re-checked here, not trusted from the reads
                // above: a concurrent delete or switch-to-private (Phase
                // 2's own update()/destroy() lock this same row) can't
                // race an attach past this point.
                $locked = Document::whereKey($document->id)->lockForUpdate()->first();

                if ($locked === null || $locked->access_level === DocumentAccessLevel::Private) {
                    abort(422, 'Private documents can\'t be attached to tasks. Change its access level first.');
                }

                $linker->attach($task, $locked);
            });
        } catch (DocumentAlreadyAttachedException $e) {
            abort(422, $e->getMessage());
        }

        return response()->json(['document' => $document->fresh('uploader')]);
    }

    /**
     * task #73 phase 2: gated by TaskPolicy::unlinkDocuments() — manage_
     * documents plus being able to view the task — not task-edit rights.
     * This is what the Documents page's own delete/edit dialogs reuse for
     * their Unlink buttons too (no separate route; see those controllers'
     * own docblocks), not just the Task edit page's inline Detach button.
     *
     * Also authorizes against the document itself and confirms it belongs
     * to the task's own company, mirroring attach() above — without
     * these, unlinkDocuments() alone (a task-scoped, manage_documents-in-
     * the-task's-org check) would let a manage_documents holder sever an
     * attachment to a document they have no visibility into at all, in an
     * org other than the task's own.
     *
     * task #73 phase 3: routed through TaskDocumentLinker so this writes
     * the same task.document_unlinked audit entry as every other unlink
     * path, instead of a raw ->detach() call with no trace of it.
     */
    public function detach(Task $task, Document $document): JsonResponse
    {
        Gate::authorize('unlinkDocuments', $task);
        Gate::authorize('view', $document);

        if ($document->organization_id !== $task->organization_id) {
            abort(422, 'Document must belong to the task\'s company.');
        }

        app(TaskDocumentLinker::class)->detach($task, $document);

        return response()->json(['detached' => true]);
    }
}
