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
        return [
            'audit_log_id' => $this->auditLog->id,
            'event_label' => $this->eventLabel,
            'entity_type' => $this->auditLog->entity_type,
            'entity_id' => $this->auditLog->entity_id,
            'entity_label' => $this->auditLog->entityLabel(),
            'message' => $this->eventLabel.': '.$this->auditLog->entityLabel().' — '.$this->auditLog->describeChanges(),
            'link' => $this->auditLog->linkUrl(),
        ];
    }
}
