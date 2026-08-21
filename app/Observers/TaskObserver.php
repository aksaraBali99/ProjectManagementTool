<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Task;
use App\Services\AuditEventNotifier;

/**
 * Single source of audit_log writes for Task mutations — replaces the
 * explicit logAudit() calls that used to live in TaskManagementController,
 * so a code path can't accidentally skip logging.
 *
 * audit_log.user_id is a required FK, so every write here is guarded by
 * auth()->check(): a Task mutated with no authenticated user (almost every
 * test fixture creates/updates Task rows directly via Eloquent, with no
 * user in scope) simply produces no audit entry, matching real usage —
 * every mutating route sits behind 'auth' middleware, so a genuine user
 * action always has auth()->id() available.
 *
 * Also the single trigger point for notification delivery (Part B) — the
 * same audit_log row this writes is handed to AuditEventNotifier, so
 * "did something notification-worthy happen" isn't detected twice.
 */
class TaskObserver
{
    public function __construct(private readonly AuditEventNotifier $notifier) {}

    public function created(Task $task): void
    {
        $this->log($task, 'task.created', collect($task->getAttributes())->only([
            'project_id', 'department_id', 'assignee_id', 'title', 'priority', 'status', 'due_date',
        ])->all());
    }

    /**
     * One row per update, not one per changed field — the action name
     * reflects whichever named change is most significant when several
     * fields change together (reassignment first, since that's what an
     * owner most needs to trace, then status, then priority), but the
     * changes payload always captures every field that actually changed,
     * not just the one the action name is drawn from.
     */
    public function updated(Task $task): void
    {
        $changes = $task->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $original = $task->getOriginal();
        $diff = collect($changes)->mapWithKeys(fn ($value, $key) => [
            $key => ['old' => $original[$key] ?? null, 'new' => $value],
        ])->all();

        $action = match (true) {
            array_key_exists('assignee_id', $changes) => 'task.reassigned',
            array_key_exists('status', $changes) => 'task.status_changed',
            array_key_exists('priority', $changes) => 'task.priority_changed',
            default => 'task.updated',
        };

        $this->log($task, $action, $diff);
    }

    public function deleted(Task $task): void
    {
        $this->log($task, 'task.deactivated');
    }

    public function restored(Task $task): void
    {
        $this->log($task, 'task.reactivated');
    }

    private function log(Task $task, string $action, array $changes = []): void
    {
        if (! auth()->check()) {
            return;
        }

        $auditLog = AuditLog::create([
            'organization_id' => $task->organization_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => 'task',
            'entity_id' => $task->id,
            'changes' => $changes,
        ]);

        $this->notifier->notify($auditLog);
    }
}
