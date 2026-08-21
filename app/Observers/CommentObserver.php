<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Comment;
use App\Services\AuditEventNotifier;

/**
 * Extends the same audit pattern to comments, so "comment added" has a
 * real audit_log entry to derive a notification event from (Part B's
 * notification event list is meant to stay consistent with what this
 * audit trail actually captures) — comments weren't in Task/Subtask's
 * original observer scope, but the two systems need the same underlying
 * "did something worth telling people about happen" detection.
 */
class CommentObserver
{
    public function __construct(private readonly AuditEventNotifier $notifier) {}

    public function created(Comment $comment): void
    {
        $this->log($comment, 'comment.created', ['body' => $comment->body]);
    }

    public function updated(Comment $comment): void
    {
        $changes = $comment->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $this->log($comment, 'comment.updated', $changes);
    }

    public function deleted(Comment $comment): void
    {
        $this->log($comment, 'comment.deleted', ['body' => $comment->body]);
    }

    private function log(Comment $comment, string $action, array $changes = []): void
    {
        if (! auth()->check()) {
            return;
        }

        $auditLog = AuditLog::create([
            'organization_id' => $comment->task->organization_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => 'comment',
            'entity_id' => $comment->id,
            'changes' => $changes,
        ]);

        $this->notifier->notify($auditLog);
    }
}
