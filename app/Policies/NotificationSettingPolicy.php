<?php

namespace App\Policies;

use App\Models\NotificationSetting;
use App\Models\User;

/**
 * A user always retains full control over their own personal preferences
 * (recipients resolving to only themselves) — that's a personal choice,
 * not an administrative action, so it's deliberately not gated by
 * manage_settings. Configuring notifications that reach anyone else
 * (specific other users, or a role-based group) requires manage_settings.
 */
class NotificationSettingPolicy
{
    /**
     * @param  array<string, mixed>|null  $recipients  the recipients payload the request is trying to create
     */
    public function create(User $user, ?array $recipients): bool
    {
        if ($user->hasPermission('manage_settings')) {
            return true;
        }

        return NotificationSetting::recipientsAreSelfOnly($recipients, $user->id);
    }

    public function update(User $user, NotificationSetting $setting): bool
    {
        if ($user->hasPermission('manage_settings')) {
            return true;
        }

        return $setting->owner_id === $user->id
            && NotificationSetting::recipientsAreSelfOnly($setting->recipients, $user->id);
    }

    public function delete(User $user, NotificationSetting $setting): bool
    {
        return $this->update($user, $setting);
    }
}
