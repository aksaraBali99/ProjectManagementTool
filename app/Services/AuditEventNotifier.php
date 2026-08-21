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
 *
 * All precedence/de-duplication logic (personal vs. admin rules,
 * collapsing overlapping admin rules) lives in NotificationSettingsResolver
 * — this class only enumerates who's even worth asking about, then fires
 * whatever channels the resolver says to.
 */
class AuditEventNotifier
{
    public function __construct(private readonly NotificationSettingsResolver $resolver) {}

    public function notify(AuditLog $auditLog): void
    {
        $eventType = $this->matchEventType($auditLog);

        if (! $eventType) {
            return;
        }

        foreach ($this->candidateUserIds($eventType, $auditLog) as $userId) {
            $user = User::find($userId);

            if (! $user) {
                continue;
            }

            $channels = $this->resolver->resolveChannels($user, $eventType, $auditLog);

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
     * Everyone worth asking the resolver about for this event: anyone with
     * a personal rule for it (any channel, active or not — an inactive
     * one still needs to reach the resolver so it can correctly block a
     * matching admin rule instead of silently falling through it), plus
     * anyone targeted by an active admin broadcast rule. The resolver
     * re-derives the actual precedence answer per user independently of
     * how they ended up in this set.
     *
     * @return list<int>
     */
    private function candidateUserIds(NotificationEventType $eventType, AuditLog $auditLog): array
    {
        $ids = NotificationSetting::where('event_type', $eventType->value)
            ->whereNull('recipients')
            ->pluck('owner_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $adminRows = NotificationSetting::where('event_type', $eventType->value)
            ->where('is_active', true)
            ->whereNotNull('recipients')
            ->get();

        foreach ($adminRows as $row) {
            if (($row->recipients['type'] ?? null) === 'users') {
                foreach ($row->recipients['ids'] ?? [] as $id) {
                    $ids[] = (int) $id;
                }
            } elseif (($row->recipients['type'] ?? null) === 'role') {
                $roleUserIds = User::whereHas(
                    'orgMemberships',
                    fn ($query) => $query->where('organization_id', $auditLog->organization_id)
                        ->whereHas('role', fn ($roleQuery) => $roleQuery->where('slug', $row->recipients['role']))
                )->pluck('id');

                foreach ($roleUserIds as $id) {
                    $ids[] = (int) $id;
                }
            }
        }

        return array_values(array_unique($ids));
    }
}
