<?php

namespace App\Services;

use App\Exceptions\DocumentAlreadyAttachedException;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Task;
use Illuminate\Database\QueryException;

/**
 * task #73 phase 3: the one place every user-driven task_documents
 * attach/detach goes through, so every one of them gets the same
 * non-silent duplicate handling and the same task.document_attached /
 * task.document_unlinked audit trail, instead of each call site
 * reimplementing (or forgetting) either.
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
            // SQLSTATE 23000 (integrity constraint violation) — the only
            // constraint an insert here can violate is the task_documents
            // (task_id, document_id) primary key itself, so this is
            // unambiguously a duplicate, on both MySQL and SQLite.
            if ((string) $e->getCode() === '23000') {
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
