<?php

namespace App\Models;

use App\Enums\NotificationChannel;
use App\Enums\NotificationEventType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['owner_id', 'event_type', 'channel', 'recipients', 'is_active'])]
class NotificationSetting extends Model
{
    protected function casts(): array
    {
        return [
            'event_type' => NotificationEventType::class,
            'channel' => NotificationChannel::class,
            'recipients' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * recipients = null (or absent) is the personal-preference convention:
     * "just notify the owner". Anything else — a specific user list or a
     * role descriptor — is an administratively-configured rule, which is
     * exactly the distinction NotificationSettingPolicy uses to decide
     * whether manage_settings is required: recipients resolving to only
     * the acting user themselves is always allowed, anything broader
     * isn't.
     *
     * @param  array<string, mixed>|null  $recipients
     */
    public static function recipientsAreSelfOnly(?array $recipients, int $userId): bool
    {
        if (empty($recipients)) {
            return true;
        }

        return ($recipients['type'] ?? null) === 'users'
            && count($recipients['ids'] ?? []) === 1
            && (int) ($recipients['ids'][0] ?? 0) === $userId;
    }

    /**
     * Two recipient descriptors are the same rule regardless of key order
     * or user-id order — a 'users' list is compared as a set, a 'role' is
     * compared by its slug.
     *
     * @param  array<string, mixed>  $a
     * @param  array<string, mixed>  $b
     */
    public static function recipientsEqual(array $a, array $b): bool
    {
        if (($a['type'] ?? null) !== ($b['type'] ?? null)) {
            return false;
        }

        if ($a['type'] === 'users') {
            $idsA = collect($a['ids'] ?? [])->map(fn ($id) => (int) $id)->sort()->values();
            $idsB = collect($b['ids'] ?? [])->map(fn ($id) => (int) $id)->sort()->values();

            return $idsA->all() === $idsB->all();
        }

        return ($a['role'] ?? null) === ($b['role'] ?? null);
    }
}
