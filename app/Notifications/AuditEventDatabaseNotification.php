<?php

namespace App\Notifications;

use App\Models\AuditLog;
use Illuminate\Notifications\Notification;

/**
 * The in-app half of a notification-worthy event — deliberately NOT
 * ShouldQueue, unlike its mail counterpart: writing one row to the
 * notifications table is fast/local, so there's no request-blocking
 * concern to queue against, and queuing it would just delay the bell
 * icon updating until a worker runs.
 */
class AuditEventDatabaseNotification extends Notification
{
    public function __construct(
        public readonly AuditLog $auditLog,
        public readonly string $eventLabel,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        // Looked up once for both keys; it names a task by code and title
        // (task #118 phase 4).
        $entityLabel = $this->auditLog->entityLabel();

        return [
            'audit_log_id' => $this->auditLog->id,
            'event_label' => $this->eventLabel,
            'entity_type' => $this->auditLog->entity_type,
            'entity_id' => $this->auditLog->entity_id,
            'entity_label' => $entityLabel,
            'message' => $this->eventLabel.': '.$entityLabel.' — '.$this->auditLog->describeChanges(),
            'link' => $this->auditLog->linkUrl(),
        ];
    }
}
