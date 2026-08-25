@extends('layouts.authenticated')

@section('title', 'Audit Trail — Solava')

@section('content')
<div>
    <a href="{{ route('settings.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Settings</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Audit Trail</h1>
    <p class="mt-1 text-[11px] text-gray-500">A read-only record of task/subtask/comment changes. Entries can never be edited or deleted.</p>

    <form method="GET" action="{{ route('audit-trail.index') }}" class="mt-4 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-white p-3">
        <div>
            <label for="organization_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Company</label>
            <select id="organization_id" name="organization_id" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <option value="">All companies</option>
                @foreach ($organizations as $org)
                    <option value="{{ $org->id }}" {{ (string) ($filters['organization_id'] ?? '') === (string) $org->id ? 'selected' : '' }}>{{ $org->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="entity_type" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Entity</label>
            <select id="entity_type" name="entity_type" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <option value="">All entities</option>
                @foreach ($entityTypes as $type)
                    <option value="{{ $type }}" {{ ($filters['entity_type'] ?? '') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="user_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">User</label>
            <select id="user_id" name="user_id" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <option value="">All users</option>
                @foreach ($users as $auditUser)
                    <option value="{{ $auditUser->id }}" {{ (string) ($filters['user_id'] ?? '') === (string) $auditUser->id ? 'selected' : '' }}>{{ $auditUser->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="action" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Action</label>
            <select id="action" name="action" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <option value="">All actions</option>
                @foreach ($actions as $actionOption)
                    <option value="{{ $actionOption }}" {{ ($filters['action'] ?? '') === $actionOption ? 'selected' : '' }}>{{ $actionOption }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="import_batch_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Import batch</label>
            <select id="import_batch_id" name="import_batch_id" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <option value="">All batches</option>
                @foreach ($importBatches as $batch)
                    <option value="{{ $batch->id }}" {{ (string) ($filters['import_batch_id'] ?? '') === (string) $batch->id ? 'selected' : '' }}>
                        Batch #{{ $batch->id }} ({{ $batch->created_at->format('M j, Y') }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="date_from" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">From</label>
            <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}"
                class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="date_to" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">To</label>
            <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}"
                class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="rounded-md bg-[#1D9E75] px-3 py-1.5 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                Filter
            </button>
            @if (array_filter($filters))
                <a href="{{ route('audit-trail.index') }}" class="text-[11px] text-gray-500 hover:underline">Clear</a>
            @endif
        </div>
    </form>

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200">
        <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
            <thead class="hidden bg-gray-50 md:table-header-group">
                <tr>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">When</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Who</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Company</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Action</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Entity</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">What changed</th>
                </tr>
            </thead>
            <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                @forelse ($entries as $entry)
                    <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0">
                        <td class="whitespace-nowrap text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">{{ $entry->created_at->format('M j, Y g:ia') }}</td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[12px] text-[#1F2937] md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Who</span>
                            {{ $entry->user->name ?? 'Unknown user' }}
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Company</span>
                            @if ($entry->organization)
                                <x-badge :background="$entry->organization->badgeBackground()" :text="$entry->organization->badgeText()">{{ $entry->organization->name }}</x-badge>
                            @else
                                —
                            @endif
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Action</span>
                            <span>
                                {{ $entry->actionLabel() }}
                                @if ($entry->importBatch)
                                    <a href="{{ route('import.review', $entry->importBatch) }}" class="ml-1 rounded bg-gray-100 px-1.5 py-0.5 text-[9px] font-normal uppercase tracking-[0.04em] text-gray-500 hover:bg-gray-200">
                                        via import #{{ $entry->importBatch->id }}
                                    </a>
                                @endif
                            </span>
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Entity</span>
                            {{ $entry->entityLabel() }}
                        </td>
                        <td class="py-1 text-[11px] text-gray-600 md:table-cell md:px-3 md:py-2.5">
                            <span class="mb-0.5 block text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">What changed</span>
                            {{ $entry->describeChanges() }}
                        </td>
                    </tr>
                @empty
                    <tr class="block md:table-row">
                        <td colspan="6" class="block px-3 py-6 text-center text-[12px] text-gray-500 md:table-cell">No audit entries match these filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $entries->links() }}
    </div>
</div>
@endsection
