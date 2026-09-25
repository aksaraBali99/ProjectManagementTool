<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Notifications\Notification;

/**
 * Fired directly from CommentController::store() when a new comment's
 * parent_comment_id is set, at the ORIGINAL (top-level) comment's author
 * — same reasoning as MentionedInCommentNotification: a reply is a direct
 * address to that specific person, not a broadcast event gated by the
 * recipient's own NotificationSetting rules, so it always goes out, no
 * opt-out. Kept as its own class rather than reusing
 * MentionedInCommentNotification even though both are "always fire" —
 * conceptually different events (being replied to vs. being @mentioned),
 * and a separate class keeps them distinguishable by type in the
 * notification history, and independent: a reply that also mentions
 * someone else fires both, correctly, for two different recipients (or
 * the same one, twice, once per reason).
 */
class RepliedToCommentNotification extends Notification
{
    public function __construct(public readonly Comment $reply) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'task_id' => $this->reply->task_id,
            'comment_id' => $this->reply->parent_comment_id,
            'message' => $this->reply->user->name.' replied to your comment on task "'.$this->reply->task->title.'".',
            'link' => route('tasks.edit', $this->reply->task_id),
        ];
    }
}
