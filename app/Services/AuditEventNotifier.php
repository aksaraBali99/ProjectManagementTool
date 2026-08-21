<?php

namespace App\Services;

use App\Enums\NotificationChannel;
use App\Enums\NotificationEventType;
use App\Models\AuditLog;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Notifications\AuditEventDatabaseNotification;
use App\Notifications\AuditEventMailNotification;

/**
 * Turns an already-written AuditLog row into notification deliveries —
 * called from the same Observers that wrote it (Part A), so there's one
 * "did something notification-worthy happen" detection, not a duplicate
 * one living separately from the audit trail.
 */
class AuditEventNotifier
{
    public function notify(AuditLog $auditLog): void
    {
        $eventType = $this->matchEventType($auditLog);

        if (! $eventType) {
            return;
        }

        foreach ($this->resolveRecipientChannels($eventType, $auditLog) as $userId => $channels) {
            $user = User::find($userId);

            if (! $user) {
                continue;
            }

            if (in_array(NotificationChannel::InApp->value, $channels, true)) {
                $user->notify(new AuditEventDatabaseNotification($auditLog, $eventType->label()));
            }

            if (in_array(NotificationChannel::Email->value, $channels, true)) {
                $user->notify(new AuditEventMailNotification($auditLog, $eventType->label()));
            }
        }
    }

    private function matchEventType(AuditLog $auditLog): ?NotificationEventType
    {
        foreach (NotificationEventType::cases() as $eventType) {
            if (in_array($auditLog->action, $eventType->matchingAuditActions(), true)) {
                return $eventType;
            }
        }

        return null;
    }

    /**
     * A user can end up a recipient via more than one rule (their own
     * personal preference AND an admin-configured broadcast rule), each
     * possibly specifying a different channel — so this collects the full
     * set of channels each recipient should receive on, rather than
     * picking just one rule per user.
     *
     * @return array<int, array<int, string>> user_id => channel values
     */
    private function resolveRecipientChannels(NotificationEventType $eventType, AuditLog $auditLog): array
    {
        $map = [];
        $addChannel = function (int $userId, string $channel) use (&$map) {
            $map[$userId] = array_values(array_unique([...($map[$userId] ?? []), $channel]));
        };

        $rows = NotificationSetting::where('event_type', $eventType->value)->where('is_active', true)->get();

        foreach ($rows as $row) {
            if (empty($row->recipients)) {
                // A self-preference row. task_assigned is special: it only
                // fires for the person who actually just became the
                // assignee ("task assigned to ME"), not for everyone who's
                // subscribed to hear about every assignment in the system.
                if ($eventType === NotificationEventType::TaskAssigned) {
                    $newAssigneeId = $this->newAssigneeId($auditLog);
                    if ($newAssigneeId !== null && (int) $row->owner_id === $newAssigneeId) {
                        $addChannel((int) $row->owner_id, $row->channel->value);
                    }

                    continue;
                }

                $addChannel((int) $row->owner_id, $row->channel->value);

                continue;
            }

            // An admin-configured broadcast row: specific users, or a role
            // resolved against THIS event's own organization — a
            // role-based rule isn't tied to one company at creation time,
            // it resolves fresh per event.
            if (($row->recipients['type'] ?? null) === 'users') {
                foreach ($row->recipients['ids'] ?? [] as $id) {
                    $addChannel((int) $id, $row->channel->value);
                }
            } elseif (($row->recipients['type'] ?? null) === 'role') {
                $roleUserIds = User::whereHas(
                    'orgMemberships',
                    fn ($query) => $query->where('organization_id', $auditLog->organization_id)
                        ->whereHas('role', fn ($roleQuery) => $roleQuery->where('slug', $row->recipients['role']))
                )->pluck('id');

                foreach ($roleUserIds as $id) {
                    $addChannel((int) $id, $row->channel->value);
                }
            }
        }

        return $map;
    }

    private function newAssigneeId(AuditLog $auditLog): ?int
    {
        $changes = $auditLog->changes ?? [];

        if (! array_key_exists('assignee_id', $changes)) {
            return null;
        }

        $value = $changes['assignee_id'];
        $newValue = is_array($value) && array_key_exists('new', $value) ? $value['new'] : $value;

        return $newValue !== null ? (int) $newValue : null;
    }
}
