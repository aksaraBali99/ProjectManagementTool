<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Task;
use App\Models\User;

/**
 * task #73 phase 2: the ONE place that answers "what is this document
 * still attached to, and what does that mean for editing/deleting it" —
 * shared by:
 *   - DocumentController::dependencies() (the preview both the Edit and
 *     Delete dialogs call before showing anything, so what they display
 *     is never trusted or duplicated across two places).
 *   - DocumentController::update()'s server-side re-check when the
 *     access level is changing (blocked-to-private / confirm-to-public).
 *   - DocumentController::destroy()'s server-side re-check inside its
 *     own transaction (the preview is never trusted there either).
 *
 * Deliberately its own service, not inlined into the controller or
 * DocumentPolicy — "the rule for files inside folders linked to tasks
 * arrives in Phase 4. Keep these checks in one place so Phase 4 can
 * extend them," per the task's own instruction.
 */
class DocumentDependencyService
{
    /**
     * @return array{
     *     linked_task_count: int,
     *     viewable_tasks: list<array{id: int, title: string}>,
     *     hidden_linked_task_count: int,
     *     client_project_count: int,
     * }
     */
    public function summarize(Document $document, User $viewer): array
    {
        // withTrashed(): "blocked while attached directly to any task" is
        // literal — a task_documents row pointing at a since-deactivated
        // (soft-deleted) task still counts. Without this, $document->tasks()
        // silently excludes it via Task's own SoftDeletingScope, so a
        // document linked only to a deactivated task would report zero
        // linked tasks here while the Documents page's own "Linked tasks"
        // column (a raw, scope-oblivious task_documents count) still shows
        // it as 1 — the exact disagreement this was flagged for.
        $linkedTaskIds = $document->tasks()->withTrashed()->pluck('tasks.id')->all();

        // Task::viewableIdsFor() — not one Gate::allows('view', $task)
        // call per linked task — see its own docblock/parity test.
        $viewableTaskIds = Task::viewableIdsFor($viewer, $linkedTaskIds)->all();
        $viewableTasks = Task::whereIn('id', $viewableTaskIds)->orderBy('title')->get(['id', 'title']);

        // "Visible to the clients of N linked projects" — N counts
        // DISTINCT PROJECTS among ALL linked tasks that have a client,
        // regardless of whether the current viewer can see those tasks
        // themselves. This is a statement about who the document would
        // become visible to, not about what this viewer happens to see.
        $clientProjectCount = empty($linkedTaskIds) ? 0 : Task::whereIn('id', $linkedTaskIds)
            ->whereHas('project.clients')
            ->distinct('project_id')
            ->count('project_id');

        return [
            'linked_task_count' => count($linkedTaskIds),
            'viewable_tasks' => $viewableTasks->map(fn (Task $task) => ['id' => $task->id, 'title' => $task->title])->values()->all(),
            'hidden_linked_task_count' => count($linkedTaskIds) - $viewableTasks->count(),
            'client_project_count' => $clientProjectCount,
        ];
    }

    /**
     * The exact shared shape/wording for "this document is still
     * attached, remove it from these tasks first" — used identically by
     * the blocked-to-private edit response and the blocked-delete
     * response ("Same response shape and wording as delete", per the
     * task's own instruction).
     *
     * @return array{message: string, linked_tasks: list<array{id: int, title: string}>, hidden_linked_task_count: int}
     */
    public function blockedResponse(array $summary): array
    {
        return [
            'message' => "This document is still attached to {$summary['linked_task_count']} tasks. Remove it from them first.",
            'linked_tasks' => $summary['viewable_tasks'],
            'hidden_linked_task_count' => $summary['hidden_linked_task_count'],
        ];
    }
}
