<?php

namespace App\Services;

use App\Exceptions\DocumentAlreadyAttachedException;
use App\Exceptions\FolderAlreadyLinkedException;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Task;
use Illuminate\Database\QueryException;

/**
 * task #73 phase 3: the one place every user-driven task_documents
 * attach/detach goes through, so every one of them gets the same
 * non-silent duplicate handling and the same task.document_attached /
 * task.document_unlinked audit trail, instead of each call site
 * reimplementing (or forgetting) either. task #73 phase 4 extends this
 * with the task_folder_links equivalent (attachFolder()/detachFolder()),
 * kept on the same class since both are "the one place a task's document
 * list gets mutated," not because folders and files share any real logic
 * beyond the duplicate-key detection helper at the bottom.
 *
 * Deliberately thin — no business-rule validation (private documents,
 * company match, view() authorization, "is this user even allowed to do
 * this at all") lives here. Those are the picker attach endpoint's own
 * concern, checked before this is ever called, since several OTHER
 * callers (chip reconciliation, the inline upload/add-link widget,
 * smart-link auto-attach) legitimately attach a document they just
 * created themselves and must never be second-guessed by rules written
 * for a completely different, user-picks-an-existing-document flow.
 *
 * The Import feature is deliberately NOT routed through this — its own
 * batch entries already audit every row it creates or links.
 */
class TaskDocumentLinker
{
    /**
     * @throws DocumentAlreadyAttachedException already linked — checked
     *                                          explicitly first (the common case), with a duplicate-key database
     *                                          error as a backstop for a genuine race rather than the only way
     *                                          this is ever detected.
     */
    public function attach(Task $task, Document $document): void
    {
        if ($task->documents()->where('document_id', $document->id)->exists()) {
            throw DocumentAlreadyAttachedException::make();
        }

        try {
            $task->documents()->attach($document->id);
        } catch (QueryException $e) {
            // SQLSTATE 23000 (integrity constraint violation) covers BOTH
            // constraints an insert here can violate: the task_documents
            // (task_id, document_id) primary key (a genuine duplicate) AND
            // its task_id/document_id foreign keys (cascadeOnDelete'd to
            // tasks/documents) — a concurrent delete of the task or the
            // document between the exists() check above and this insert
            // raises the identical SQLSTATE for a real integrity failure,
            // not a duplicate. isDuplicateKeyViolation() narrows to only
            // the primary-key case; anything else re-throws, so a masked
            // data-integrity failure is never mislabeled as "Already
            // attached." — a driver-code check (MySQL's 1062 vs SQLite's
            // own extended codes) would need per-driver branching, so this
            // checks the message text instead, which both drivers'
            // duplicate-key errors reliably contain.
            if ($this->isDuplicateKeyViolation($e)) {
                throw DocumentAlreadyAttachedException::make();
            }

            throw $e;
        }

        AuditLog::create([
            'organization_id' => $task->organization_id,
            'user_id' => auth()->id(),
            'action' => 'task.document_attached',
            'entity_type' => 'task',
            'entity_id' => $task->id,
            'changes' => ['document_id' => $document->id, 'document_name' => $document->name],
        ]);
    }

    /**
     * task #73 phase 4: the folder-link sibling of attach() above. A
     * deliberately separate, parallel method rather than one generic
     * method branching on file-vs-folder — the two differ in real ways
     * (different pivot table/columns, different audit action names, no
     * DocumentAlreadyAttachedException-style class to share since the
     * failure mode, while structurally identical, is a genuinely
     * different exception type callers need to catch independently) and
     * a single branching method would need a type parameter threaded
     * through regardless. linked_by (who attached it) is recorded on the
     * pivot row itself, not just implied by the audit entry's user_id.
     *
     * @throws FolderAlreadyLinkedException already linked — checked
     *                                      explicitly first, with a duplicate-key database error as a
     *                                      backstop for a genuine race.
     */
    public function attachFolder(Task $task, DocumentFolder $folder): void
    {
        if ($task->folders()->where('folder_id', $folder->id)->exists()) {
            throw FolderAlreadyLinkedException::make();
        }

        try {
            $task->folders()->attach($folder->id, ['linked_by' => auth()->id()]);
        } catch (QueryException $e) {
            // Same SQLSTATE-23000 ambiguity as attach() above:
            // task_folder_links' task_id/folder_id columns are also
            // foreign keys (restrictOnDelete rather than cascade here, but
            // a concurrent unlink-then-delete race can still land here) —
            // isDuplicateKeyViolation() below is shared, generic detection
            // logic, not Document-specific despite this class's name.
            if ($this->isDuplicateKeyViolation($e)) {
                throw FolderAlreadyLinkedException::make();
            }

            throw $e;
        }

        AuditLog::create([
            'organization_id' => $task->organization_id,
            'user_id' => auth()->id(),
            'action' => 'task.folder_attached',
            'entity_type' => 'task',
            'entity_id' => $task->id,
            'changes' => ['folder_id' => $folder->id, 'folder_name' => $folder->name],
        ]);
    }

    /**
     * Removes only the task_folder_links row — the folder and its contents
     * are entirely untouched, same "unlink never deletes" rule as detach()
     * above. No audit entry when nothing was actually linked.
     */
    public function detachFolder(Task $task, DocumentFolder $folder): void
    {
        $deleted = $task->folders()->detach($folder->id);

        if ($deleted === 0) {
            return;
        }

        AuditLog::create([
            'organization_id' => $task->organization_id,
            'user_id' => auth()->id(),
            'action' => 'task.folder_unlinked',
            'entity_type' => 'task',
            'entity_id' => $task->id,
            'changes' => ['folder_id' => $folder->id, 'folder_name' => $folder->name],
        ]);
    }

    /**
     * True only for a genuine primary-key violation — task_documents'
     * (task_id, document_id) or task_folder_links' (task_id, folder_id) —
     * never for either table's own foreign-key violations. Both share
     * SQLSTATE 23000, so the SQLSTATE alone can't tell them apart. MySQL's
     * duplicate-key error always contains "Duplicate entry"; SQLite's
     * always contains "UNIQUE constraint failed" (a primary key is
     * enforced as a unique index there). A foreign-key violation on either
     * driver contains neither phrase. Shared by attach() and
     * attachFolder() above — generic detection logic, not tied to either
     * table's specific columns.
     */
    private function isDuplicateKeyViolation(QueryException $e): bool
    {
        if ((string) $e->getCode() !== '23000') {
            return false;
        }

        $message = $e->getMessage();

        return str_contains($message, 'Duplicate entry') || str_contains($message, 'UNIQUE constraint failed');
    }

    /**
     * Removes only the link — the document itself is untouched. No audit
     * entry when nothing was actually attached (an already-absent link
     * detached again is a no-op, not an event).
     */
    public function detach(Task $task, Document $document): void
    {
        $deleted = $task->documents()->detach($document->id);

        if ($deleted === 0) {
            return;
        }

        AuditLog::create([
            'organization_id' => $task->organization_id,
            'user_id' => auth()->id(),
            'action' => 'task.document_unlinked',
            'entity_type' => 'task',
            'entity_id' => $task->id,
            'changes' => ['document_id' => $document->id, 'document_name' => $document->name],
        ]);
    }
}
