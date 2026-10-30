@extends('layouts.authenticated')

@section('title', 'Tasks — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Tasks</h1>
        <div class="flex items-center gap-3">
            @if ($organization && $canCreate)
                <a href="{{ route('tasks.create', ['project' => $organization->projects()->orderBy('name')->first(), 'return_to' => url()->full(), 'return_label' => 'Tasks']) }}"
                   class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                    + Add task
                </a>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-brand-50 px-3 py-2 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        @php
            // Only carry org-agnostic state across a company-tab switch — a
            // project/department/assignee filter is a set of IDs scoped to
            // the *current* company, so it wouldn't mean the same thing (or
            // might match nothing at all) after switching companies.
            $tabQueryParts = array_filter([
                'show_inactive' => $showInactive ? 1 : null,
                'sort' => $sort !== 'due_date' ? $sort : null,
                'direction' => $direction !== 'asc' ? $direction : null,
                'q' => $filters['q'] ?? null,
                'priority' => $filters['priority'] ?? null,
                'status' => $filters['status'] ?? null,
                'due_from' => $filters['due_from'] ?? null,
                'due_to' => $filters['due_to'] ?? null,
                'completed' => $filters['completed'] ?? null,
                'completed_from' => $filters['completed_from'] ?? null,
                'completed_to' => $filters['completed_to'] ?? null,
            ], fn ($value) => $value !== null && $value !== '');
            $tabQuery = http_build_query($tabQueryParts);

            $sortLink = function (string $column) use ($organization, $showInactive, $filters, $sort, $direction) {
                $nextDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
                $parts = array_filter(array_merge($filters, [
                    'show_inactive' => $showInactive ? 1 : null,
                    'sort' => $column,
                    'direction' => $nextDirection,
                ]), fn ($value) => $value !== null && $value !== '');

                return [
                    'href' => route('tasks.index', $organization).'?'.http_build_query($parts),
                    'indicator' => $sort === $column ? ($direction === 'asc' ? '↑' : '↓') : '',
                ];
            };
        @endphp

        <form method="GET" action="{{ route('tasks.index', $organization) }}" class="mt-4 flex flex-wrap items-end gap-3 rounded-lg border border-gray-200 bg-white p-3">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">
            {{-- The "Show inactive tasks" checkbox below has no name (it
                 navigates on change instead of submitting), so without this
                 the Filter button would silently switch inactive tasks off. --}}
            @if ($showInactive)
                <input type="hidden" name="show_inactive" value="1">
            @endif

            <div>
                <label for="task-filter-q" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Title</label>
                <input id="task-filter-q" name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Search title…"
                    class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            </div>

            <div>
                <label for="task-filter-project" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Project</label>
                <select id="task-filter-project" name="project_id" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="">All projects</option>
                    @foreach ($filterProjects as $filterProject)
                        <option value="{{ $filterProject->id }}" {{ (string) ($filters['project_id'] ?? '') === (string) $filterProject->id ? 'selected' : '' }}>{{ $filterProject->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="task-filter-department" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Department</label>
                <select id="task-filter-department" name="department_id" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="">All departments</option>
                    @foreach ($filterDepartments as $filterDepartment)
                        <option value="{{ $filterDepartment->id }}" {{ (string) ($filters['department_id'] ?? '') === (string) $filterDepartment->id ? 'selected' : '' }}>{{ $filterDepartment->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="task-filter-assignee" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Assignee</label>
                <select id="task-filter-assignee" name="assignee_id" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="">All assignees</option>
                    <option value="unassigned" {{ ($filters['assignee_id'] ?? '') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                    @foreach ($filterAssignees as $filterAssignee)
                        <option value="{{ $filterAssignee->id }}" {{ (string) ($filters['assignee_id'] ?? '') === (string) $filterAssignee->id ? 'selected' : '' }}>{{ $filterAssignee->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="task-filter-priority" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</label>
                <select id="task-filter-priority" name="priority" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="">All priorities</option>
                    @foreach (\App\Enums\Priority::cases() as $priorityCase)
                        <option value="{{ $priorityCase->value }}" {{ ($filters['priority'] ?? '') === $priorityCase->value ? 'selected' : '' }}>{{ $priorityCase->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="task-filter-status" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</label>
                <select id="task-filter-status" name="status" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="">All statuses</option>
                    @foreach (\App\Enums\TaskStatus::cases() as $statusCase)
                        <option value="{{ $statusCase->value }}" {{ ($filters['status'] ?? '') === $statusCase->value ? 'selected' : '' }}>{{ $statusCase->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="task-filter-due-from" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Due from</label>
                <input id="task-filter-due-from" name="due_from" type="date" value="{{ $filters['due_from'] ?? '' }}"
                    class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            </div>

            <div>
                <label for="task-filter-due-to" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Due to</label>
                <input id="task-filter-due-to" name="due_to" type="date" value="{{ $filters['due_to'] ?? '' }}"
                    class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            </div>

            {{-- task #72 phase 7: completed tasks older than the company's
                 window are hidden unless "All" or a date range is chosen.
                 The range inputs only show for "Date range". --}}
            @php
                $completedMode = ! empty($filters['completed_from']) || ! empty($filters['completed_to'])
                    ? 'range'
                    : (in_array($filters['completed'] ?? '', ['all', 'range'], true) ? $filters['completed'] : '');
            @endphp
            <div>
                <label for="task-filter-completed" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Completed</label>
                <select id="task-filter-completed" name="completed" class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600"
                    title="Recent: completed in the last {{ $organization->completed_window_days }} days"
                    onchange="const range = document.getElementById('task-filter-completed-range'); range.hidden = this.value !== 'range'; if (range.hidden) { range.querySelectorAll('input').forEach(function (input) { input.value = ''; }); }">
                    <option value="" {{ $completedMode === '' ? 'selected' : '' }}>Recent ({{ $organization->completed_window_days }} days)</option>
                    <option value="all" {{ $completedMode === 'all' ? 'selected' : '' }}>All</option>
                    <option value="range" {{ $completedMode === 'range' ? 'selected' : '' }}>Date range</option>
                </select>
            </div>

            <div id="task-filter-completed-range" class="flex items-end gap-3" {{ $completedMode === 'range' ? '' : 'hidden' }}>
                <div>
                    <label for="task-filter-completed-from" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Completed from</label>
                    <input id="task-filter-completed-from" name="completed_from" type="date" value="{{ $filters['completed_from'] ?? '' }}"
                        class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                </div>
                <div>
                    <label for="task-filter-completed-to" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Completed to</label>
                    <input id="task-filter-completed-to" name="completed_to" type="date" value="{{ $filters['completed_to'] ?? '' }}"
                        class="mt-1 rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                </div>
            </div>
            @if ($completedMode === 'range' && ! empty($filters['status']) && $filters['status'] !== \App\Enums\TaskStatus::Completed->value)
                {{-- A completed date range only matches Completed tasks,
                     so any other Status can't match anything. --}}
                <p class="task-filter-range-hint basis-full text-[10px] text-[#A32D2D]">A completed date range only shows Completed tasks, so it can't match Status = {{ \App\Enums\TaskStatus::tryFrom($filters['status'])?->label() ?? $filters['status'] }}. Clear the Status filter to see them.</p>
            @endif

            <div class="flex items-center gap-2">
                <button type="submit" class="rounded-md bg-brand-600 px-3 py-1.5 text-[12px] font-medium text-white hover:bg-brand-700">
                    Filter
                </button>
                @if (array_filter($filters))
                    <a href="{{ route('tasks.index', $organization).($showInactive ? '?show_inactive=1' : '') }}" class="text-[11px] text-gray-500 hover:underline">Clear</a>
                @endif
            </div>

            <label class="ml-auto flex items-center gap-1.5 text-[11px] text-gray-600">
                <input type="checkbox" {{ $showInactive ? 'checked' : '' }}
                    onchange="const params = new URLSearchParams(window.location.search); params.delete('show_inactive'); params.delete('page'); if (this.checked) { params.set('show_inactive', '1'); } window.location = '{{ route('tasks.index', $organization) }}' + (params.toString() ? '?' + params.toString() : '');">
                Show inactive tasks
            </label>
        </form>

        <x-company-tabs :organizations="$organizations" :active="$organization" route="tasks.index" :query="$tabQuery">
        {{-- Below md, the table becomes a stack of row-cards: each <tr>
             is a bordered block instead of a table row, and every <td>
             carries its own inline label (md:hidden) since the column
             headers themselves are hidden below md rather than repeated
             per row. At md and up this reverts to a normal table, unchanged
             from before. --}}
        <div class="overflow-hidden rounded-lg border border-gray-200 md:overflow-x-auto">
            <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                <x-table-header>
                    <th class="w-8 px-3 py-2"></th>
                    @foreach ([
                        'title' => 'Title',
                        'project' => 'Project',
                        'department' => 'Department',
                        'assignee' => 'Assignee',
                        'priority' => 'Priority',
                        'status' => 'Status',
                        'due_date' => 'Due',
                        'completed' => 'Completed',
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
                    @if ($showInactive)
                        @php
                            $link = $sortLink('active');
                        @endphp
                        <x-th>
                            <a href="{{ $link['href'] }}" class="inline-flex items-center gap-1 hover:text-gray-700">
                                Active
                                <span class="text-gray-400">{{ $link['indicator'] }}</span>
                            </a>
                        </x-th>
                    @endif
                    <x-th align="right">Actions</x-th>
                </x-table-header>
                <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                    @forelse ($tasks as $task)
                        @php
                            $canEditTask = auth()->user()->can('update', $task);
                            $canDeactivateTask = auth()->user()->can('delete', $task);
                        @endphp
                        <tr class="task-row block px-3 py-2.5 md:table-row md:px-0 md:py-0" data-drilldown-toggle="{{ $task->id }}">
                            <td class="hidden md:table-cell md:px-3 md:py-2.5">
                                <button type="button" class="drilldown-btn text-[11px] text-gray-500 hover:text-gray-700" data-target="{{ $task->id }}">+</button>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">
                                <button type="button" class="drilldown-btn text-[11px] text-gray-500 hover:text-gray-700 md:hidden" data-target="{{ $task->id }}">+</button>
                                <a href="{{ route('tasks.edit', ['task' => $task, 'return_to' => url()->full(), 'return_label' => 'Tasks']) }}" class="flex-1 hover:underline">{{ $task->title }}</a>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Project</span>
                                {{ $task->project->name }}
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Department</span>
                                <x-badge :background="$task->department->badgeBackground()" :text="$task->department->badgeText()">{{ $task->department->name }}</x-badge>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Assignee</span>
                                @if ($task->assignee)
                                    <span class="inline-flex items-center gap-1.5">
                                        <x-avatar :user="$task->assignee" size="18px" />
                                        {{ \Illuminate\Support\Str::before($task->assignee->name, ' ') }}
                                    </span>
                                @else
                                    Unassigned
                                @endif
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Priority</span>
                                <x-badge :background="$task->priority->badgeBackground()" :text="$task->priority->badgeText()">{{ $task->priority->label() }}</x-badge>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Status</span>
                                <x-badge :background="$task->status->badgeBackground()" :text="$task->status->badgeText()">{{ $task->status->label() }}</x-badge>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Due</span>
                                {{ $task->due_date?->format('M j') ?? '—' }}
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Completed</span>
                                {{ $task->completed_at?->format('M j') ?? '—' }}
                            </td>
                            @if ($showInactive)
                                <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                    <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Active</span>
                                    @if ($task->trashed())
                                        <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Inactive</span>
                                    @else
                                        <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Active</span>
                                    @endif
                                </td>
                            @endif
                            <td class="flex items-center justify-end gap-2 py-1 text-[11px] md:table-cell md:px-3 md:py-2.5 md:text-right">
                                @if ($canEditTask)
                                    <a href="{{ route('tasks.edit', ['task' => $task, 'return_to' => url()->full(), 'return_label' => 'Tasks']) }}" class="text-brand-600 hover:underline">Edit</a>
                                @endif
                                @if ($canDeactivateTask)
                                    <form method="POST" action="{{ route('tasks.toggle-active', $task) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="ml-3 text-gray-500 hover:underline">
                                            {{ $task->trashed() ? 'Activate' : 'Deactivate' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        <tr class="drilldown-row block border-t border-gray-100 md:table-row" data-drilldown="{{ $task->id }}" style="display: none;">
                            <td class="hidden md:table-cell"></td>
                            <td colspan="{{ $showInactive ? 9 : 8 }}" class="block bg-gray-50 px-3 py-3 md:table-cell">
                                {{-- task #72 phase 2: empty until the row is first
                                     opened, then filled from tasks.preview. Rendering
                                     the description and the full _subtasks/_comments
                                     partials here for every task — opened or not —
                                     was this page's entire remaining cost. --}}
                                <div class="task-preview"
                                    data-preview-url="{{ route('tasks.preview', ['task' => $task, 'return_to' => url()->full(), 'return_label' => 'Tasks']) }}"
                                    data-open-url="{{ route('tasks.edit', ['task' => $task, 'return_to' => url()->full(), 'return_label' => 'Tasks']) }}">
                                    <p class="task-preview-loading text-[11px] text-gray-500">Loading…</p>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <x-empty-table-row :colspan="$showInactive ? 10 : 9">
                            {{ array_filter($filters) ? 'No tasks match these filters.' : 'No tasks yet.' }}
                        </x-empty-table-row>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- task #72 phase 4: one page at a time. The count line, then the
             page links and the "Per page" box on one row (stacked below md). --}}
        <div class="mt-3 flex flex-col items-end gap-2">
            @if ($tasks->total() > 0)
                <p class="tasks-showing text-[11px] text-gray-500">
                    Showing {{ number_format($tasks->firstItem()) }}–{{ number_format($tasks->lastItem()) }} of {{ number_format($tasks->total()) }} {{ \Illuminate\Support\Str::plural('task', $tasks->total()) }}
                </p>
            @endif

            <div class="flex w-full flex-col items-end gap-2 md:w-auto md:flex-row md:items-center md:gap-4">
                {{ $tasks->links('tasks._pagination-links') }}

                <form method="POST" action="{{ route('tasks.per-page') }}" class="flex items-center gap-1.5" data-per-page-form>
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="return_to" value="{{ url()->full() }}">
                    <label for="tasks-per-page" class="text-[11px] text-gray-500">Per page</label>
                    {{-- type="text", not "number": a number box with max="100"
                         would make the browser refuse 150 on Enter, and the
                         server's clamp and hint would never happen. --}}
                    <input id="tasks-per-page" name="per_page" type="text" inputmode="numeric" autocomplete="off"
                        value="{{ $perPage }}" data-current="{{ $perPage }}"
                        class="w-14 rounded-md border border-gray-300 px-2 py-1 text-center text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                </form>
            </div>

            @if (session('tasks_per_page_hint'))
                <p class="tasks-per-page-hint text-[11px] text-gray-500">{{ session('tasks_per_page_hint') }}</p>
            @endif
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

                if (isHidden) {
                    loadPreview(target.querySelector('.task-preview'));
                }
            });
        });

        // task #72 phase 2: the drilldown's contents arrive from
        // tasks.preview on FIRST open only. Reopening a row shows the HTML
        // already in the DOM, so open/close after that is instant and
        // costs nothing.
        function loadPreview(container) {
            if (! container || container.dataset.loaded === '1' || container.dataset.loading === '1') {
                // Nothing to do, already have it, or a request is still in
                // flight — double clicks must not fire a second fetch.
                return;
            }

            container.dataset.loading = '1';
            // Re-shown on a retry too: without this the previous attempt's
            // error text just sits there for the whole of the new request,
            // and the click looks like it did nothing.
            showPreviewLoading(container);

            fetch(container.dataset.previewUrl, {
                credentials: 'same-origin',
                headers: { 'Accept': 'text/html' },
            })
                .then(function (response) {
                    // An expired session is the failure this page will
                    // actually hit — it can sit open for hours. The request
                    // asks for text/html, so Laravel's auth middleware
                    // REDIRECTS to /login rather than returning 401, fetch
                    // follows that by default, and response.ok is true. Left
                    // unchecked, the login form's HTML (and a stale CSRF
                    // token) gets injected into a table cell and the row is
                    // stuck that way with no retry possible.
                    if (response.redirected) throw new Error('redirected to ' + response.url);
                    if (! response.ok) throw new Error('HTTP ' + response.status);

                    return response.text();
                })
                .then(function (html) {
                    container.innerHTML = html;

                    // Mentions, code blocks, video thumbnails, file chips and
                    // link previews are all render-time enhancements, so
                    // injected HTML needs this pass the way a page load gets
                    // one. Scoped to the container, not document, so
                    // reopening one row never re-walks the whole page.
                    // Click behaviour (lightbox, chips, link previews) is
                    // delegated on document in app.js and needs nothing here.
                    //
                    // Guarded separately from the fetch: the fragment has
                    // already rendered correctly by this point, so a throw in
                    // here must not send us to the catch below and replace it
                    // with an error message.
                    try {
                        if (window.solavaRichText) {
                            window.solavaRichText.highlight(container);
                        }
                    } catch (error) {
                        // Enhancements are cosmetic; the content stands without them.
                    }

                    // Set LAST, so the row is only marked done once the
                    // fragment is actually in place.
                    container.dataset.loaded = '1';
                })
                .catch(function () {
                    // Left un-loaded on purpose: the next open retries.
                    // Built with DOM calls rather than an innerHTML string so
                    // the URL's own & separators need no escaping.
                    const message = document.createElement('p');
                    message.className = 'task-preview-error text-[11px] text-gray-500';
                    message.append("Couldn't load this preview. ");

                    const link = document.createElement('a');
                    link.href = container.dataset.openUrl;
                    link.className = 'font-medium text-brand-600 hover:underline';
                    link.textContent = 'Open task';
                    message.append(link);

                    container.replaceChildren(message);
                })
                .finally(function () {
                    delete container.dataset.loading;
                });
        }

        function showPreviewLoading(container) {
            const loading = document.createElement('p');
            loading.className = 'task-preview-loading text-[11px] text-gray-500';
            loading.textContent = 'Loading…';
            container.replaceChildren(loading);
        }

        // task #72 phase 4: the "Per page" box saves on Enter or when
        // leaving it, never on each keystroke, and only if the value
        // changed. Anything that isn't a whole number (empty, "abc", "2.5")
        // just puts the current value back. 0 or 150 ARE sent: the server
        // clamps them to 1–100 and the list says so.
        const perPageForm = document.querySelector('[data-per-page-form]');
        if (perPageForm) {
            const perPageInput = perPageForm.querySelector('input[name="per_page"]');
            const currentPerPage = perPageInput.dataset.current;
            let perPageSubmitting = false;

            const shouldSavePerPage = function () {
                const value = perPageInput.value.trim();
                if (! /^-?\d+$/.test(value) || String(parseInt(value, 10)) === currentPerPage) {
                    perPageInput.value = currentPerPage;

                    return false;
                }

                return true;
            };

            perPageForm.addEventListener('submit', function (event) {
                if (perPageSubmitting || ! shouldSavePerPage()) {
                    event.preventDefault();

                    return;
                }
                perPageSubmitting = true;
            });

            perPageInput.addEventListener('blur', function () {
                // Enter already submitted; the page unloading blurs the box.
                if (perPageSubmitting || ! shouldSavePerPage()) return;
                perPageSubmitting = true;
                perPageForm.submit();
            });
        }
    })();
</script>
@endsection
