@extends('layouts.authenticated')

@section('title', 'Notification Settings — Solava')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('settings.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Settings</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Notification Settings</h1>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-brand-50 px-3 py-2 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    <h2 class="mt-6 text-[12px] font-semibold uppercase tracking-[0.05em] text-gray-500">My Notification Preferences</h2>
    <p class="mt-1 text-[11px] text-gray-500">Choose which events notify you personally, and how. This is your own preference — no special permission needed.</p>

    <form method="POST" action="{{ route('notification-settings.update-mine') }}" class="mt-3">
        @csrf
        @method('PUT')

        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Event</th>
                        @foreach ($channels as $channel)
                            <th class="px-3 py-2 text-center text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">{{ $channel->label() }}</th>
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
                                        class="rounded border-gray-300 text-brand-600 focus:ring-brand-600">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="submit" class="mt-3 rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
            Save my preferences
        </button>
    </form>

    @if ($canManageOthers)
        <h2 class="mt-8 text-[12px] font-semibold uppercase tracking-[0.05em] text-gray-500">Team Notification Rules</h2>
        <p class="mt-1 text-[11px] text-gray-500">Notify specific people, or an entire role, when an event happens. Requires manage_settings.</p>

        <form method="POST" action="{{ route('notification-settings.rules.store') }}" class="mt-3 space-y-3 rounded-lg border border-gray-200 bg-white p-3">
            @csrf

            @error('duplicate')
                <p class="field-error text-[11px] text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="rule_event_type" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Event</label>
                    <select id="rule_event_type" name="event_type" required class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                        @foreach ($eventTypes as $eventType)
                            <option value="{{ $eventType->value }}">{{ $eventType->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rule_channel" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Channel</label>
                    <select id="rule_channel" name="channel" required class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                        @foreach ($channels as $channel)
                            <option value="{{ $channel->value }}">{{ $channel->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-start gap-6">
                <label class="flex items-center gap-1.5 text-[11px] text-gray-700">
                    <input type="radio" name="recipient_type" value="users" checked class="text-brand-600 focus:ring-brand-600">
                    Specific users
                </label>
                <select name="user_ids[]" multiple size="4" class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    @foreach ($users as $option)
                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                    @endforeach
                </select>

                <label class="flex items-center gap-1.5 text-[11px] text-gray-700">
                    <input type="radio" name="recipient_type" value="role" class="text-brand-600 focus:ring-brand-600">
                    Everyone with a role
                </label>
                <select name="role" class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
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

            <button type="submit" class="rounded-md border border-gray-300 px-3 py-1.5 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                + Add rule
            </button>
        </form>

        <div class="mt-3 overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                <x-table-header>
                    <x-th>Event</x-th>
                    <x-th>Channel</x-th>
                    <x-th>Recipients</x-th>
                    <x-th>Configured by</x-th>
                    <x-th>Status</x-th>
                    <x-th align="right">Actions</x-th>
                </x-table-header>
                <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                    @forelse ($rules as $rule)
                        <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0">
                            <td class="text-[12px] text-[#1F2937] md:table-cell md:px-3 md:py-2.5">{{ $rule->event_type->label() }}</td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Channel</span>
                                {{ $rule->channel->label() }}
                            </td>
                            <td class="py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="mb-0.5 block text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Recipients</span>
                                @if (($rule->recipients['type'] ?? null) === 'role')
                                    Role: {{ $roles->firstWhere('slug', $rule->recipients['role'])?->name ?? ucfirst($rule->recipients['role']) }}
                                @else
                                    Users: {{ \App\Models\User::whereIn('id', $rule->recipients['ids'] ?? [])->pluck('name')->implode(', ') }}
                                @endif
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Configured by</span>
                                {{ $rule->owner->name ?? '—' }}
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Status</span>
                                @if ($rule->is_active)
                                    <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Active</span>
                                @else
                                    <span class="rounded-sm bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="flex items-center justify-end gap-2 py-1 md:table-cell md:px-3 md:py-2.5 md:text-right">
                                <form method="POST" action="{{ route('notification-settings.rules.toggle', $rule) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-[11px] text-brand-600 hover:underline">{{ $rule->is_active ? 'Deactivate' : 'Activate' }}</button>
                                </form>
                                <form method="POST" action="{{ route('notification-settings.rules.destroy', $rule) }}" class="ml-2 inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] text-gray-500 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <x-empty-table-row colspan="6" py="6">No team rules configured yet.</x-empty-table-row>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
