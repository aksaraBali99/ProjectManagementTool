<?php

namespace App\Http\Controllers;

use App\Enums\NotificationChannel;
use App\Enums\NotificationEventType;
use App\Models\NotificationSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NotificationSettingsController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $mine = NotificationSetting::where('owner_id', $user->id)->whereNull('recipients')->get()
            ->keyBy(fn (NotificationSetting $setting) => $setting->event_type->value.':'.$setting->channel->value);

        $canManageOthers = $user->hasPermission('manage_settings');

        $rules = $canManageOthers
            ? NotificationSetting::whereNotNull('recipients')->with('owner')->orderByDesc('id')->get()
            : collect();

        return view('notification-settings.index', [
            'eventTypes' => NotificationEventType::cases(),
            'channels' => NotificationChannel::cases(),
            'mine' => $mine,
            'canManageOthers' => $canManageOthers,
            'rules' => $rules,
            'users' => $canManageOthers ? User::orderBy('name')->get(['id', 'name']) : collect(),
            'roles' => $canManageOthers ? Role::whereIn('slug', [Role::MANAGEMENT, Role::STAFF, Role::CLIENT])->get() : collect(),
        ]);
    }

    /**
     * The personal-preferences grid: one checkbox per (event type, channel)
     * pair, always scoped to the current user — there's no owner_id input
     * in this request at all, so there's no way to target anyone else
     * through this endpoint by construction, not just by policy.
     */
    public function updateMine(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $enabled = $request->input('preferences', []);

        foreach (NotificationEventType::cases() as $eventType) {
            foreach (NotificationChannel::cases() as $channel) {
                $isActive = ! empty($enabled[$eventType->value][$channel->value]);

                $setting = NotificationSetting::where('owner_id', $user->id)
                    ->where('event_type', $eventType->value)
                    ->where('channel', $channel->value)
                    ->whereNull('recipients')
                    ->first();

                if ($setting) {
                    $setting->update(['is_active' => $isActive]);
                } else {
                    NotificationSetting::create([
                        'owner_id' => $user->id,
                        'event_type' => $eventType->value,
                        'channel' => $channel->value,
                        'recipients' => null,
                        'is_active' => $isActive,
                    ]);
                }
            }
        }

        return redirect()->route('notification-settings.index')->with('status', 'Your notification preferences were saved.');
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'event_type' => ['required', 'in:'.implode(',', NotificationEventType::values())],
            'channel' => ['required', 'in:'.implode(',', NotificationChannel::values())],
            'recipient_type' => ['required', 'in:users,role'],
            'user_ids' => ['required_if:recipient_type,users', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'role' => ['required_if:recipient_type,role', 'in:'.implode(',', [Role::MANAGEMENT, Role::STAFF, Role::CLIENT])],
        ], [
            'user_ids.required_if' => 'Please select a user when the recipient type is users.',
            'role.required_if' => 'Please select a role when the recipient type is role.',
        ]);

        $recipients = $data['recipient_type'] === 'users'
            ? ['type' => 'users', 'ids' => array_map('intval', $data['user_ids'])]
            : ['type' => 'role', 'role' => $data['role']];

        Gate::authorize('create', [NotificationSetting::class, $recipients]);

        $duplicate = NotificationSetting::where('event_type', $data['event_type'])
            ->where('channel', $data['channel'])
            ->whereNotNull('recipients')
            ->get()
            ->contains(fn (NotificationSetting $existing) => NotificationSetting::recipientsEqual($existing->recipients, $recipients));

        if ($duplicate) {
            return back()->withInput()->withErrors([
                'duplicate' => 'A rule for this event, channel, and recipient(s) already exists — toggle or edit the existing one instead.',
            ]);
        }

        NotificationSetting::create([
            'owner_id' => auth()->id(),
            'event_type' => $data['event_type'],
            'channel' => $data['channel'],
            'recipients' => $recipients,
            'is_active' => true,
        ]);

        return redirect()->route('notification-settings.index')->with('status', 'Notification rule created.');
    }

    public function toggleRule(NotificationSetting $notificationSetting): RedirectResponse
    {
        Gate::authorize('update', $notificationSetting);

        $notificationSetting->update(['is_active' => ! $notificationSetting->is_active]);

        return redirect()->route('notification-settings.index')->with('status', 'Notification rule updated.');
    }

    public function destroyRule(NotificationSetting $notificationSetting): RedirectResponse
    {
        Gate::authorize('delete', $notificationSetting);

        $notificationSetting->delete();

        return redirect()->route('notification-settings.index')->with('status', 'Notification rule deleted.');
    }
}
