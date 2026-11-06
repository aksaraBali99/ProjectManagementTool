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
        // Looked up once for the subject and the body. task #118 phase 4:
        // it names the task by code and title ("BGS-0012 Fix invoice PDF
        // layout"). This email is queued, so the label is the task's as of
        // sending, not as of the event: a task moved in between shows its
        // new code.
        $entityLabel = $this->auditLog->entityLabel();

        $message = (new MailMessage)
            ->subject($this->eventLabel.': '.$entityLabel)
            ->line($this->eventLabel)
            ->line($entityLabel)
            ->line($this->auditLog->describeChanges());

        if ($link = $this->auditLog->linkUrl()) {
            $message->action('View task', $link);
        }

        return $message;
    }
}
