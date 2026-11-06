<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Notifications\Notification;

/**
 * Fired directly per mentioned user from CommentController — unlike the
 * AuditEventNotifier-driven notifications, an @mention is a direct address
 * to a specific person, not a broadcast event gated by the recipient's own
 * NotificationSetting rules, so it always goes out. In-app only (no mail
 * queue), matching AuditEventDatabaseNotification's reasoning: writing one
 * row is fast/local, nothing here needs queuing.
 */
class MentionedInCommentNotification extends Notification
{
    public function __construct(public readonly Task $task) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            // task #118 phase 4: the code outside the quotes, the title in
            // them: 'You are mentioned in task BGS-0012 "Fix invoice PDF
            // layout".' ($task->code loads the company if needed.)
            'message' => 'You are mentioned in task '.($this->task->code === null ? '' : $this->task->code.' ').'"'.$this->task->title.'".',
            'link' => route('tasks.edit', $this->task->id),
        ];
    }
}
