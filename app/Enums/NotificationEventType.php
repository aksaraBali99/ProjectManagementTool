<?php

namespace App\Enums;

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
     * TaskAssigned matches both a fresh assignment at creation time and a
     * later reassignment — either way, someone became the assignee.
     *
     * @return array<int, string>
     */
    public function matchingAuditActions(): array
    {
        return match ($this) {
            self::TaskStatusChanged => ['task.status_changed'],
            self::TaskAssigned => ['task.created', 'task.reassigned'],
            self::CommentAdded => ['comment.created'],
            self::TaskPriorityChanged => ['task.priority_changed'],
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
