@extends('layouts.authenticated')

@section('title', 'Projects — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Projects</h1>
        @if ($organization && auth()->user()->can('create', [\App\Models\Project::class, $organization->id]))
            <a href="{{ route('projects.create', $organization) }}"
               class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                + Add project
            </a>
        @endif
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-brand-50 px-3 py-2 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        @php
            // Only carry org-agnostic state across a company-tab switch — a
            // client filter is a set of user IDs scoped to the *current*
            // company's attached clients, so it wouldn't mean the same
            // thing (or might match nothing at all) after switching
            // companies.
            $tabQueryParts = array_filter([
                'sort' => $sort !== 'name' ? $sort : null,
                'direction' => $direction !== 'asc' ? $direction : null,
                'q' => $filters['q'] ?? null,
                'status' => $filters['status'] ?? null,
                'priority' => $filters['priority'] ?? null,
            ], fn ($value) => $value !== null && $value !== '');
            $tabQuery = http_build_query($tabQueryParts);

            $sortLink = function (string $column) use ($organization, $filters, $sort, $direction) {
                $nextDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
                $parts = array_filter(array_merge($filters, [
                    'sort' => $column,
                    'direction' => $nextDirection,
                ]), fn ($value) => $value !== null && $value !== '');

                return [
                    'href' => route('projects.index', $organization).'?'.http_build_query($parts),
                    'indicator' => $sort === $column ? ($direction === 'asc' ? '↑' : '↓') : '',
                ];
            };
        @endphp

        <form method="GET" action="{{ route('projects.index', $organization) }}" class="mt-4 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-white p-3">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">

            <div>
                <label for="project-filter-q" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name</label>
                <input id="project-filter-q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Search name…"
                    class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            </div>

            <div>
                <label for="project-filter-client" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Client</label>
                <select id="project-filter-client" name="client_id" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="">All clients</option>
                    <option value="internal" {{ ($filters['client_id'] ?? '') === 'internal' ? 'selected' : '' }}>Internal</option>
                    @foreach ($filterClients as $filterClient)
                        <option value="{{ $filterClient->id }}" {{ (string) ($filters['client_id'] ?? '') === (string) $filterClient->id ? 'selected' : '' }}>{{ $filterClient->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="project-filter-status" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</label>
                <select id="project-filter-status" name="status" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="">All statuses</option>
                    @foreach (\App\Enums\ProjectStatus::cases() as $statusCase)
                        <option value="{{ $statusCase->value }}" {{ ($filters['status'] ?? '') === $statusCase->value ? 'selected' : '' }}>{{ $statusCase->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="project-filter-priority" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</label>
                <select id="project-filter-priority" name="priority" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="">All priorities</option>
                    @foreach (\App\Enums\Priority::cases() as $priorityCase)
                        <option value="{{ $priorityCase->value }}" {{ ($filters['priority'] ?? '') === $priorityCase->value ? 'selected' : '' }}>{{ $priorityCase->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="rounded-md bg-brand-600 px-3 py-1.5 text-[12px] font-medium text-white hover:bg-brand-700">
                    Filter
                </button>
                @if (array_filter($filters))
                    <a href="{{ route('projects.index', $organization) }}" class="text-[11px] text-gray-500 hover:underline">Clear</a>
                @endif
            </div>
        </form>

        <x-company-tabs :organizations="$organizations" :active="$organization" route="projects.index" :query="$tabQuery">
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                <x-table-header>
                    <th class="w-8 px-3 py-2"></th>
                    @foreach ([
                        'name' => 'Name',
                        'client' => 'Client',
                        'status' => 'Status',
                        'priority' => 'Priority',
                        'tasks' => 'Tasks',
                    ] as $column => $label)
                        @php
                            $link = $sortLink($column);
                        @endphp
                        <x-th>
                            <a href="{{ $link['href'] }}" class="inline-flex items-center gap-1 hover:text-gray-700">
                                {{ $label }}
                                <span class="text-gray-400">{{ $link['indicator'] }}</span>
                            </a>
                        </x-th>
                    @endforeach
                    <x-th align="right">Actions</x-th>
                </x-table-header>
                <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                    @forelse ($projects as $project)
                        <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0" data-drilldown-toggle="{{ $project->id }}">
                            <td class="hidden md:table-cell md:px-3 md:py-2.5">
                                <button type="button" class="drilldown-btn text-[11px] text-gray-500 hover:text-gray-700" data-target="{{ $project->id }}">+</button>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">
                                <button type="button" class="drilldown-btn text-[11px] text-gray-500 hover:text-gray-700 md:hidden" data-target="{{ $project->id }}">+</button>
                                {{ $project->name }}
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Client</span>
                                {{ $project->primaryClient()->name ?? 'Internal' }}
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Status</span>
                                @if ($project->status === \App\Enums\ProjectStatus::Open)
                                    <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">{{ $project->status->label() }}</span>
                                @else
                                    <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">{{ $project->status->label() }}</span>
                                @endif
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Priority</span>
                                <x-badge :background="$project->priority->badgeBackground()" :text="$project->priority->badgeText()">{{ $project->priority->label() }}</x-badge>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Tasks</span>
                                {{ $project->tasks_count }}
                            </td>
                            <td class="flex items-center justify-end gap-2 py-1 text-[11px] md:table-cell md:px-3 md:py-2.5 md:text-right">
                                @can('update', $project)
                                    <a href="{{ route('projects.edit', $project) }}" class="text-brand-600 hover:underline">Edit</a>
                                @endcan
                                @if (! empty(auth()->user()->manageableOrganizationIds()))
                                    <a href="{{ route('projects.template', $project) }}" class="ml-3 text-gray-500 hover:underline">Use as template</a>
                                @endif
                            </td>
                        </tr>
                        <tr class="drilldown-row block border-t border-gray-100 md:table-row" data-drilldown="{{ $project->id }}" style="display: none;">
                            <td class="hidden md:table-cell"></td>
                            <td colspan="6" class="block bg-gray-50 px-3 py-3 md:table-cell">
                                <div class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Tasks</div>
                                @if ($project->tasks->isEmpty())
                                    <p class="mt-1 text-[12px] text-gray-500">No tasks on this project yet.</p>
                                @else
                                    <div class="mt-2 overflow-hidden rounded-md border border-gray-200 bg-white">
                                        <table class="w-full">
                                            <x-table-header bg="gray-100">
                                                <x-th>Title</x-th>
                                                <x-th>Department</x-th>
                                                <x-th>Assignee</x-th>
                                                <x-th>Priority</x-th>
                                                <x-th>Status</x-th>
                                                <x-th>Due</x-th>
                                            </x-table-header>
                                            <tbody class="block divide-y divide-gray-100 md:table-row-group">
                                                @foreach ($project->tasks as $task)
                                                    <tr class="block px-3 py-2 md:table-row md:px-0 md:py-0">
                                                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2">
                                                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Title</span>
                                                            <a href="{{ route('tasks.edit', $task) }}" class="hover:underline">{{ $task->title }}</a>
                                                        </td>
                                                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2">
                                                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Department</span>
                                                            <x-badge :background="$task->department->badgeBackground()" :text="$task->department->badgeText()">{{ $task->department->name }}</x-badge>
                                                        </td>
                                                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2">
                                                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Assignee</span>
                                                            {{ $task->assignee->name ?? 'Unassigned' }}
                                                        </td>
                                                        <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2">
                                                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Priority</span>
                                                            <x-badge :background="$task->priority->badgeBackground()" :text="$task->priority->badgeText()">{{ $task->priority->label() }}</x-badge>
                                                        </td>
                                                        <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2">
                                                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Status</span>
                                                            <x-badge :background="$task->status->badgeBackground()" :text="$task->status->badgeText()">{{ $task->status->label() }}</x-badge>
                                                        </td>
                                                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2">
                                                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Due</span>
                                                            {{ $task->due_date?->format('M j') ?? '—' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-empty-table-row colspan="7">
                            {{ array_filter($filters) ? 'No projects match these filters.' : 'No projects yet.' }}
                        </x-empty-table-row>
                    @endforelse
                </tbody>
            </table>
        </div>
        </x-company-tabs>
    @endif
</div>

<script>
    (function () {
        // Each row renders two .drilldown-btn copies (one for the desktop
        // table layout, one inline in the mobile card layout) so only one
        // is ever visible at a given breakpoint — sync both on click so
        // neither shows stale +/− state if the viewport is later resized
        // across md without a page reload.
        document.querySelectorAll('.drilldown-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const target = document.querySelector('[data-drilldown="' + btn.dataset.target + '"]');
                if (! target) return;
                const isHidden = target.style.display === 'none';
                target.style.display = isHidden ? '' : 'none';
                document.querySelectorAll('.drilldown-btn[data-target="' + btn.dataset.target + '"]').forEach(function (twin) {
                    twin.textContent = isHidden ? '−' : '+';
                });
            });
        });
    })();
</script>
@endsection
