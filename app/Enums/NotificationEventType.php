<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

/**
 * The curated set of notification-worthy events — deliberately a subset
 * of the raw audit_log 'action' strings (which also include things like
 * task.updated, subtask.deleted that aren't meant to be user-facing
 * notification triggers), but each case maps to real audit_log actions so
 * the two systems stay derived from the same underlying "did something
 * happen" detection rather than duplicating it. See
 * matchingAuditActions() for the exact mapping the shared Observer uses.
 */
enum NotificationEventType: string
{
    use HasEnumValues;

    case TaskStatusChanged = 'task_status_changed';
    case TaskAssigned = 'task_assigned';
    case CommentAdded = 'comment_added';
    case TaskPriorityChanged = 'task_priority_changed';

    public function label(): string
    {
        return match ($this) {
            self::TaskStatusChanged => 'Task status changed',
            self::TaskAssigned => 'Task assigned to me',
            self::CommentAdded => 'Comment added',
            self::TaskPriorityChanged => 'Task priority changed',
        };
    }

    /**
     * The audit_log 'action' value(s) that mean this event happened.
     * TaskAssigned matches a fresh assignment or reassignment on either a
     * task or one of its subtasks — "assigned to me" means the same thing
     * either way, and NotificationSettingsResolver::isNewAssignee() reads
     * the same 'assignee_id' changes shape regardless of entity_type, so
     * no other part of the pipeline needs to know which one fired.
     *
     * @return array<int, string>
     */
    public function matchingAuditActions(): array
    {
        return match ($this) {
            self::TaskStatusChanged => ['task.status_changed'],
            self::TaskAssigned => ['task.created', 'task.reassigned', 'subtask.created', 'subtask.reassigned'],
            self::CommentAdded => ['comment.created'],
            self::TaskPriorityChanged => ['task.priority_changed'],
        };
    }
}
