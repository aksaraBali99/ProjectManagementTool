@extends('layouts.authenticated')

@section('title', 'Tasks — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Tasks</h1>
        <div class="flex items-center gap-3">
            @if ($organization && $canAddDocuments)
                <button type="button" onclick="document.getElementById('add-document-modal').showModal()"
                    class="rounded-[8px] border border-gray-300 px-4 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                    + Add new document
                </button>
            @endif
            @if ($organization && $canCreate)
                <a href="{{ route('tasks.create', $organization->projects()->orderBy('name')->first()) }}"
                   class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                    + Add task
                </a>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-[8px] bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        <div class="mt-4 flex items-center justify-between border-b border-gray-200">
            <div class="flex gap-1">
                @foreach ($organizations as $tab)
                    @php $isActiveTab = $organization && $organization->id === $tab->id @endphp
                    <a href="{{ route('tasks.index', $tab) }}{{ $showInactive ? '?show_inactive=1' : '' }}"
                       style="{{ $isActiveTab ? 'border-color: '.$tab->accent_color : '' }}"
                       class="border-b-2 px-4 py-2 text-[12px] {{ $isActiveTab ? 'font-medium text-[#1F2937]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        {{ $tab->name }}
                    </a>
                @endforeach
            </div>

            <label class="mb-2 flex items-center gap-1.5 text-[11px] text-gray-600">
                <input type="checkbox" {{ $showInactive ? 'checked' : '' }}
                    onchange="window.location = '{{ route('tasks.index', $organization) }}' + (this.checked ? '?show_inactive=1' : '')">
                Show inactive tasks
            </label>
        </div>

        <div class="mt-4 overflow-hidden rounded-[10px] border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="w-8 px-3 py-2"></th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Title</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Project</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Department</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Assignee</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Due</th>
                        @if ($showInactive)
                            <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Active</th>
                        @endif
                        <th class="px-3 py-2 text-right text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($tasks as $task)
                        @php
                            $canEditTask = auth()->user()->can('update', $task);
                            $canDeactivateTask = auth()->user()->can('delete', $task);
                        @endphp
                        <tr class="task-row" data-drilldown-toggle="{{ $task->id }}">
                            <td class="px-3 py-2.5">
                                <button type="button" class="drilldown-btn text-[11px] text-gray-500 hover:text-gray-700" data-target="{{ $task->id }}">+</button>
                            </td>
                            <td class="px-3 py-2.5 text-[12px] font-medium text-[#1F2937]">
                                <a href="{{ route('tasks.edit', $task) }}" class="hover:underline">{{ $task->title }}</a>
                            </td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $task->project->name }}</td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $task->department->name }}</td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                            <td class="px-3 py-2.5">
                                @if ($task->priority === \App\Enums\Priority::High)
                                    <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">{{ $task->priority->label() }}</span>
                                @elseif ($task->priority === \App\Enums\Priority::Medium)
                                    <span class="rounded-[5px] bg-[#FDF1D9] px-2 py-0.5 text-[10px] font-medium text-[#8A5A00]">{{ $task->priority->label() }}</span>
                                @else
                                    <span class="rounded-[5px] bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">{{ $task->priority->label() }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $task->status->label() }}</td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $task->due_date?->format('M j') ?? '—' }}</td>
                            @if ($showInactive)
                                <td class="px-3 py-2.5">
                                    @if ($task->trashed())
                                        <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Inactive</span>
                                    @else
                                        <span class="rounded-[5px] bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Active</span>
                                    @endif
                                </td>
                            @endif
                            <td class="px-3 py-2.5 text-right text-[11px]">
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
                        <tr class="drilldown-row" data-drilldown="{{ $task->id }}" style="display: none;">
                            <td></td>
                            <td colspan="{{ $showInactive ? 8 : 7 }}" class="bg-gray-50 px-3 py-3">
                                <div class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description</div>
                                <p class="mt-1 text-[12px] text-gray-700">{{ $task->description ?: 'No description.' }}</p>

                                <div class="mt-3 text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Subtasks</div>
                                <div class="mt-1">
                                    @include('tasks._subtasks', ['task' => $task, 'canEdit' => $canEditTask, 'staffOptions' => $staffOptions])
                                </div>

                                <div class="mt-3 text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Comments</div>
                                <div class="mt-1">
                                    @include('tasks._comments', ['task' => $task])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $showInactive ? 9 : 8 }}" class="px-3 py-4 text-center text-[12px] text-gray-500">No tasks yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

@if ($organization && $canAddDocuments)
    <dialog id="add-document-modal" class="w-full max-w-sm rounded-[12px] border border-gray-200 p-6 shadow-lg backdrop:bg-black/30">
        <h2 class="text-[14px] font-medium text-[#1F2937]">Add document</h2>
        <p class="mt-1 text-[11px] text-gray-500">Adds to {{ $organization->name }}'s document library — not attached to any task yet.</p>

        @if ($errors->any())
            <div class="mt-3 rounded-[8px] bg-red-50 p-3 text-[12px] text-red-700">
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
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <div>
                <label for="document-link" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Link</label>
                <input id="document-link" name="link" type="url" required placeholder="https://…"
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <div>
                <label for="document-access" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Access level</label>
                <select id="document-access" name="access_level" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
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
                <button type="submit" class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
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
        document.querySelectorAll('.drilldown-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const target = document.querySelector('[data-drilldown="' + btn.dataset.target + '"]');
                if (! target) return;
                const isHidden = target.style.display === 'none';
                target.style.display = isHidden ? '' : 'none';
                btn.textContent = isHidden ? '−' : '+';
            });
        });
    })();
</script>
@endsection
