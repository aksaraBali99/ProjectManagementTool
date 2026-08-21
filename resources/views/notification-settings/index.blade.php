@extends('layouts.authenticated')

@section('title', 'Notification Settings — Solava')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('settings.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Settings</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Notification Settings</h1>

    @if (session('status'))
        <div class="mt-4 rounded-[8px] bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    <h2 class="mt-6 text-[12px] font-semibold uppercase tracking-[0.05em] text-gray-500">My Notification Preferences</h2>
    <p class="mt-1 text-[11px] text-gray-500">Choose which events notify you personally, and how. This is your own preference — no special permission needed.</p>

    <form method="POST" action="{{ route('notification-settings.update-mine') }}" class="mt-3">
        @csrf
        @method('PUT')

        <div class="overflow-hidden rounded-[10px] border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Event</th>
                        @foreach ($channels as $channel)
                            <th class="px-3 py-2 text-center text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">{{ $channel->label() }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($eventTypes as $eventType)
                        <tr>
                            <td class="px-3 py-2.5 text-[12px] text-[#1F2937]">{{ $eventType->label() }}</td>
                            @foreach ($channels as $channel)
                                @php $existing = $mine->get($eventType->value.':'.$channel->value) @endphp
                                <td class="px-3 py-2.5 text-center">
                                    <input type="checkbox"
                                        name="preferences[{{ $eventType->value }}][{{ $channel->value }}]"
                                        {{ $existing?->is_active ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit" class="mt-3 rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
            Save my preferences
        </button>
    </form>

    @if ($canManageOthers)
        <h2 class="mt-8 text-[12px] font-semibold uppercase tracking-[0.05em] text-gray-500">Team Notification Rules</h2>
        <p class="mt-1 text-[11px] text-gray-500">Notify specific people, or an entire role, when an event happens. Requires manage_settings.</p>

        <form method="POST" action="{{ route('notification-settings.rules.store') }}" class="mt-3 space-y-3 rounded-[10px] border border-gray-200 bg-white p-3">
            @csrf

            @error('duplicate')
                <p class="field-error text-[11px] text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="rule_event_type" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Event</label>
                    <select id="rule_event_type" name="event_type" required class="mt-1 rounded-[8px] border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                        @foreach ($eventTypes as $eventType)
                            <option value="{{ $eventType->value }}">{{ $eventType->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rule_channel" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Channel</label>
                    <select id="rule_channel" name="channel" required class="mt-1 rounded-[8px] border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                        @foreach ($channels as $channel)
                            <option value="{{ $channel->value }}">{{ $channel->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-start gap-6">
                <label class="flex items-center gap-1.5 text-[11px] text-gray-700">
                    <input type="radio" name="recipient_type" value="users" checked class="text-[#1D9E75] focus:ring-[#1D9E75]">
                    Specific users
                </label>
                <select name="user_ids[]" multiple size="4" class="rounded-[8px] border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    @foreach ($users as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </select>

                <label class="flex items-center gap-1.5 text-[11px] text-gray-700">
                    <input type="radio" name="recipient_type" value="role" class="text-[#1D9E75] focus:ring-[#1D9E75]">
                    Everyone with a role
                </label>
                <select name="role" class="rounded-[8px] border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    @foreach ($roles as $roleOption)
                        <option value="{{ $roleOption->slug }}">{{ $roleOption->name }}</option>
                    @endforeach
                </select>
            </div>

            @error('user_ids')
                <p class="field-error text-[11px] text-red-600">{{ $message }}</p>
            @enderror
            @error('role')
                <p class="field-error text-[11px] text-red-600">{{ $message }}</p>
            @enderror

            <button type="submit" class="rounded-[8px] border border-gray-300 px-3 py-1.5 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                + Add rule
            </button>
        </form>

        <div class="mt-3 overflow-hidden rounded-[10px] border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Event</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Channel</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Recipients</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Configured by</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</th>
                        <th class="px-3 py-2 text-right text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($rules as $rule)
                        <tr>
                            <td class="px-3 py-2.5 text-[12px] text-[#1F2937]">{{ $rule->event_type->label() }}</td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $rule->channel->label() }}</td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">
                                @if (($rule->recipients['type'] ?? null) === 'role')
                                    Role: {{ $roles->firstWhere('slug', $rule->recipients['role'])?->name ?? ucfirst($rule->recipients['role']) }}
                                @else
                                    Users: {{ \App\Models\User::whereIn('id', $rule->recipients['ids'] ?? [])->pluck('name')->implode(', ') }}
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $rule->owner->name ?? '—' }}</td>
                            <td class="px-3 py-2.5">
                                @if ($rule->is_active)
                                    <span class="rounded-[5px] bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Active</span>
                                @else
                                    <span class="rounded-[5px] bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-right">
                                <form method="POST" action="{{ route('notification-settings.rules.toggle', $rule) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-[11px] text-[#1D9E75] hover:underline">{{ $rule->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                                <form method="POST" action="{{ route('notification-settings.rules.destroy', $rule) }}" class="ml-2 inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] text-gray-500 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-6 text-center text-[12px] text-gray-500">No team rules configured yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
