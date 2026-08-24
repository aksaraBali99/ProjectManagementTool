<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Subtask;
use App\Services\AuditEventNotifier;

/**
 * Same pattern as TaskObserver — see its docblock for the auth()->check()
 * guard rationale, and for why AuditEventNotifier is invoked from here too.
 * subtask.created/subtask.reassigned feed the TaskAssigned event type (see
 * NotificationEventType::matchingAuditActions()) so "notify me when I'm
 * assigned" covers subtask assignment the same as task assignment; every
 * other subtask action still maps to no event type, so notify() stays a
 * no-op for those. A subtask's own organization_id has to be read off its
 * parent task, since Subtask doesn't carry that column itself.
 */
class SubtaskObserver
{
    public function __construct(private readonly AuditEventNotifier $notifier) {}

    public function created(Subtask $subtask): void
    {
        $this->log($subtask, 'subtask.created', collect($subtask->getAttributes())->only([
            'title', 'assignee_id', 'due_date',
        ])->all());
    }

    public function updated(Subtask $subtask): void
    {
        $changes = $subtask->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $original = $subtask->getOriginal();
        $diff = collect($changes)->mapWithKeys(fn ($value, $key) => [
            $key => ['old' => $original[$key] ?? null, 'new' => $value],
        ])->all();

        $action = match (true) {
            array_key_exists('is_done', $changes) => 'subtask.status_changed',
            array_key_exists('assignee_id', $changes) => 'subtask.reassigned',
            default => 'subtask.updated',
        };

        $this->log($subtask, $action, $diff);
    }

    /**
     * A hard delete (Subtask has no SoftDeletes), so unlike Task's
     * deactivate/reactivate the record itself is about to be gone — worth
     * a snapshot of what it was, not just the fact that it was deleted.
     * The model instance still holds its attributes in memory at this
     * point even though the DB row is already gone.
     */
    public function deleted(Subtask $subtask): void
    {
        $this->log($subtask, 'subtask.deleted', collect($subtask->getAttributes())->only([
            'title', 'assignee_id', 'due_date', 'is_done',
        ])->all());
    }

    private function log(Subtask $subtask, string $action, array $changes = []): void
    {
        if (! auth()->check()) {
            return;
        }

        $auditLog = AuditLog::create([
            'organization_id' => $subtask->task->organization_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => 'subtask',
            'entity_id' => $subtask->id,
            'changes' => $changes,
        ]);

        $this->notifier->notify($auditLog);
    }
}
