<?php

namespace App\Services;

use App\Enums\NotificationEventType;
use App\Models\AuditLog;
use App\Models\NotificationSetting;
use App\Models\User;

/**
 * The single place that decides which channels (if any) a specific user
 * should be notified on for a specific event. Two rules this centralizes
 * so they can't drift apart across call sites:
 *
 * 1. Precedence — a personal rule (recipients IS NULL, owner_id = the
 *    user) always wins over admin-configured broadcast rules for that
 *    same user + event_type, whether the personal rule is active or not.
 *    A user who muted an event stays muted even if an admin's broadcast
 *    rule would otherwise have included them — the admin rule only
 *    applies to users who never configured a personal preference for
 *    that event_type at all. Personal preferences are stored one row per
 *    channel (see NotificationSettingsController::updateMine()), so
 *    "has a personal rule" means "has ANY row for this event_type with
 *    recipients null", not "has one for this specific channel".
 * 2. De-duplication — a user can match more than one admin broadcast
 *    rule for the same event_type (e.g. two overlapping role rules).
 *    They still resolve to one delivery per channel, not one per
 *    matching row.
 */
class NotificationSettingsResolver
{
    /**
     * @return list<string> resolved NotificationChannel values; empty
     *                      means this user should not be notified.
     */
    public function resolveChannels(User $user, NotificationEventType $eventType, AuditLog $auditLog): array
    {
        $personalRows = NotificationSetting::where('owner_id', $user->id)
            ->where('event_type', $eventType->value)
            ->whereNull('recipients')
            ->get();

        if ($personalRows->isNotEmpty()) {
            // task_assigned's personal preference means "notify me when
            // *I* am assigned" — it only applies to the instance where
            // this user is the actual new assignee, not to every
            // assignment event system-wide. Since this user owns a
            // personal rule for the event, the admin path stays blocked
            // regardless — an assignment they weren't part of simply
            // produces no notification at all, not a fallback to admin.
            if ($eventType === NotificationEventType::TaskAssigned && ! $this->isNewAssignee($auditLog, $user->id)) {
                return [];
            }

            return $personalRows->where('is_active', true)
                ->pluck('channel')
                ->map(fn ($channel) => $channel->value)
                ->unique()
                ->values()
                ->all();
        }

        return $this->resolveAdminChannels($user, $eventType, $auditLog);
    }

    public function shouldNotify(User $user, NotificationEventType $eventType, AuditLog $auditLog): bool
    {
        return $this->resolveChannels($user, $eventType, $auditLog) !== [];
    }

    /**
     * @return list<string>
     */
    private function resolveAdminChannels(User $user, NotificationEventType $eventType, AuditLog $auditLog): array
    {
        $channels = [];

        $rows = NotificationSetting::where('event_type', $eventType->value)
            ->where('is_active', true)
            ->whereNotNull('recipients')
            ->get();

        foreach ($rows as $row) {
            if ($this->rowTargetsUser($row, $user, $auditLog->organization_id)) {
                $channels[] = $row->channel->value;
            }
        }

        return array_values(array_unique($channels));
    }

    private function rowTargetsUser(NotificationSetting $row, User $user, int $organizationId): bool
    {
        if (($row->recipients['type'] ?? null) === 'users') {
            return in_array($user->id, array_map('intval', $row->recipients['ids'] ?? []), true);
        }

        if (($row->recipients['type'] ?? null) === 'role') {
            return $user->orgMemberships()
                ->where('organization_id', $organizationId)
                ->whereHas('role', fn ($query) => $query->where('slug', $row->recipients['role']))
                ->exists();
        }

        return false;
    }

    private function isNewAssignee(AuditLog $auditLog, int $userId): bool
    {
        $changes = $auditLog->changes ?? [];

        if (! array_key_exists('assignee_id', $changes)) {
            return false;
        }

        $value = $changes['assignee_id'];
        $newValue = is_array($value) && array_key_exists('new', $value) ? $value['new'] : $value;

        return $newValue !== null && (int) $newValue === $userId;
    }
}
