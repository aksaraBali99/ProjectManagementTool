<?php

namespace App\Notifications;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * The email half of a notification-worthy event. ShouldQueue so sending
 * doesn't block the request that triggered it (a task update, a comment
 * post, ...) on an SMTP round-trip — dispatched onto the 'database' queue
 * connection already configured in this app, no Redis needed.
 */
class AuditEventMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly AuditLog $auditLog,
        public readonly string $eventLabel,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject($this->eventLabel.': '.$this->auditLog->entityLabel())
            ->line($this->eventLabel)
            ->line($this->auditLog->entityLabel())
            ->line($this->auditLog->describeChanges());

        if ($link = $this->auditLog->linkUrl()) {
            $message->action('View task', $link);
        }

        return $message;
    }
}
