@extends('layouts.authenticated')

@section('title', 'Tasks — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Tasks</h1>
        <div class="flex items-center gap-3">
            @if ($organization && $canAddDocuments)
                <button type="button" onclick="document.getElementById('add-document-modal').showModal()"
                    class="rounded-md border border-gray-300 px-4 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                    + Add new document
                </button>
            @endif
            @if ($organization && $canCreate)
                <a href="{{ route('tasks.create', $organization->projects()->orderBy('name')->first()) }}"
                   class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                    + Add task
                </a>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        <div class="mt-4 flex justify-end">
            <label class="flex items-center gap-1.5 text-[11px] text-gray-600">
                <input type="checkbox" {{ $showInactive ? 'checked' : '' }}
                    onchange="window.location = '{{ route('tasks.index', $organization) }}' + (this.checked ? '?show_inactive=1' : '')">
                Show inactive tasks
            </label>
        </div>

        <x-company-tabs :organizations="$organizations" :active="$organization" route="tasks.index" :query="$showInactive ? 'show_inactive=1' : ''">
        {{-- Below md, the table becomes a stack of row-cards: each <tr>
             is a bordered block instead of a table row, and every <td>
             carries its own inline label (md:hidden) since the column
             headers themselves are hidden below md rather than repeated
             per row. At md and up this reverts to a normal table, unchanged
             from before. --}}
        <div class="overflow-hidden rounded-lg border border-gray-200 md:overflow-x-auto">
            <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                <thead class="hidden bg-gray-50 md:table-header-group">
                    <tr>
                        <th class="w-8 px-3 py-2"></th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Title</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Project</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Department</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Assignee</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Priority</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Due</th>
                        @if ($showInactive)
                            <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Active</th>
                        @endif
                        <th class="px-3 py-2 text-right text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Actions</th>
                    </tr>
                </thead>
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
                                <a href="{{ route('tasks.edit', $task) }}" class="flex-1 hover:underline">{{ $task->title }}</a>
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
                                    <a href="{{ route('tasks.edit', $task) }}" class="text-[#1D9E75] hover:underline">Edit</a>
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
                            <td colspan="{{ $showInactive ? 8 : 7 }}" class="block bg-gray-50 px-3 py-3 md:table-cell">
                                <div class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description</div>
                                <p class="mt-1 text-[12px] text-gray-700">{{ $task->description ?: 'No description.' }}</p>

                                <div class="mt-3 text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Subtasks</div>
                                <div class="mt-1">
                                    @include('tasks._subtasks', ['task' => $task, 'canEdit' => $canEditTask, 'staffOptions' => $staffByProject[$task->project_id] ?? []])
                                </div>

                                <div class="mt-3 text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Comments</div>
                                <div class="mt-1">
                                    @include('tasks._comments', ['task' => $task, 'mentionableUsers' => collect($staffByProject[$task->project_id] ?? [])->reject(fn ($member) => $member['id'] === auth()->id())->values()])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="{{ $showInactive ? 9 : 8 }}" class="block px-3 py-4 text-center text-[12px] text-gray-500 md:table-cell">No tasks yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </x-company-tabs>
    @endif
</div>

@if ($organization && $canAddDocuments)
    <dialog id="add-document-modal" class="w-full max-w-sm rounded-lg border border-gray-200 p-6 shadow-lg backdrop:bg-black/30">
        <h2 class="text-[14px] font-medium text-[#1F2937]">Add document</h2>
        <p class="mt-1 text-[11px] text-gray-500">Adds to {{ $organization->name }}'s document library — not attached to any task yet.</p>

        @if ($errors->any())
            <div class="mt-3 rounded-md bg-red-50 p-3 text-[12px] text-red-700">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('documents.store') }}" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="organization_id" value="{{ $organization->id }}">

            <div>
                <label for="document-name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name</label>
                <input id="document-name" name="name" type="text" required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <div>
                <label for="document-link" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Link</label>
                <input id="document-link" name="link" type="url" required placeholder="https://…"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <div>
                <label for="document-access" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Access level</label>
                <select id="document-access" name="access_level" required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    @foreach (\App\Enums\DocumentAccessLevel::cases() as $accessCase)
                        <option value="{{ $accessCase->value }}" {{ $accessCase === \App\Enums\DocumentAccessLevel::Internal ? 'selected' : '' }}>{{ $accessCase->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-document-modal').close()"
                    class="text-[12px] text-gray-600 hover:underline">
                    Cancel
                </button>
                <button type="submit" class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                    Add document
                </button>
            </div>
        </form>
    </dialog>

    @if ($errors->any())
        <script>document.getElementById('add-document-modal').showModal();</script>
    @endif
@endif

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
