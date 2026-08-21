@extends('layouts.authenticated')

@section('title', 'Dashboard — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Dashboard</h1>

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">{{ $emptyMessage ?? "You don't have access to any companies yet." }}</p>
    @else
        <div class="mt-4 flex gap-1 border-b border-gray-200">
            @foreach ($organizations as $tab)
                @php $isActiveTab = $organization && $organization->id === $tab->id @endphp
                <a href="{{ route('dashboard', $tab) }}"
                   style="border-color: {{ $tab->accent_color }}"
                   class="border-b-2 px-4 py-2 text-[12px] {{ $isActiveTab ? 'font-medium text-[#1F2937]' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $tab->name }}
                </a>
            @endforeach
        </div>

        <div class="mt-4 rounded-lg border border-[#FCA5A5] bg-[#FEF2F2]">
            <div class="flex items-center justify-between border-b border-[#FCA5A5] px-3 py-2">
                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-[#B91C1C]">Active</span>
                <span class="text-[10px] text-[#B91C1C]">{{ $activeTasks->count() }}</span>
            </div>
            <div class="divide-y divide-[#FECACA]">
                @forelse ($activeTasks as $task)
                    <a href="{{ route('tasks.edit', $task) }}" class="flex items-center justify-between px-3 py-2 hover:bg-[#FEE2E2]">
                        <div>
                            <p class="text-[12px] font-medium text-[#1F2937]">{{ $task->title }}</p>
                            <p class="mt-0.5 text-[10px] text-gray-500">
                                {{ $task->project->name }}
                                @if ($task->assignee) &middot; <x-avatar :user="$task->assignee" size="16px" class="align-middle" /> {{ $task->assignee->name }} @endif
                                @if ($task->due_date) &middot; {{ $task->due_date->format('M j') }} @endif
                            </p>
                        </div>
                        <x-badge :background="$task->status->badgeBackground()" :text="$task->status->badgeText()">{{ $task->status->label() }}</x-badge>
                    </a>
                @empty
                    <p class="px-3 py-4 text-center text-[11px] text-gray-400">No active high-priority tasks</p>
                @endforelse
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach (\App\Enums\Priority::cases() as $priority)
                @php $group = $priorityGroups[$priority->value] ?? collect() @endphp
                <div class="rounded-lg border border-gray-200 bg-white">
                    <div class="flex items-center justify-between border-b border-gray-200 px-3 py-2">
                        <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">{{ $priority->label() }}</span>
                        <span class="text-[10px] text-gray-400">{{ $group->count() }}</span>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($group as $task)
                            <a href="{{ route('tasks.edit', $task) }}" class="block px-3 py-2 hover:bg-gray-50">
                                <p class="text-[12px] font-medium text-[#1F2937]">{{ $task->title }}</p>
                                <p class="mt-0.5 text-[10px] text-gray-500">
                                    {{ $task->project->name }}
                                    @if ($task->assignee) &middot; <x-avatar :user="$task->assignee" size="16px" class="align-middle" /> {{ $task->assignee->name }} @endif
                                    @if ($task->due_date) &middot; {{ $task->due_date->format('M j') }} @endif
                                </p>
                            </a>
                        @empty
                            <p class="px-3 py-4 text-center text-[11px] text-gray-400">No tasks</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Client falls through to mode 'none': their entire visible task
             set is already project-scoped and shown in full above (Active +
             Priority groups), so a MyTask section here would just repeat it. --}}
        @if ($myTaskMode !== 'none')
        <div class="mt-4 rounded-lg border border-gray-200 bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 px-3 py-2">
                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">My Task</span>
                <span class="text-[10px] text-gray-400">{{ $myTasks->count() }}</span>
            </div>

            @if ($myTaskMode === 'admin' && $staffOptions->isNotEmpty())
                <form method="GET" action="{{ route('dashboard', $organization) }}" class="flex flex-wrap items-center gap-3 border-b border-gray-100 px-3 py-2">
                    @foreach ($staffOptions as $staffMember)
                        <label class="flex items-center gap-1.5 text-[11px] text-gray-600">
                            <input type="checkbox" name="staff[]" value="{{ $staffMember->id }}" {{ in_array($staffMember->id, $selectedStaffIds) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]">
                            {{ $staffMember->name }}
                        </label>
                    @endforeach
                    <button type="submit" class="rounded-md border border-gray-300 px-3 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50">
                        Filter
                    </button>
                    @if (! empty($selectedStaffIds))
                        <a href="{{ route('dashboard', $organization) }}" class="text-[11px] text-gray-500 hover:underline">Clear</a>
                    @endif
                </form>
            @endif

            <div class="divide-y divide-gray-100">
                @forelse ($myTasks as $task)
                    @php
                        // For staff, a task can show up here because a subtask
                        // (not the task itself) is assigned to them — surface
                        // which subtask, so they can find it once they drill
                        // into the task.
                        $myViaSubtask = $myTaskMode === 'staff' && $task->assignee_id !== auth()->id()
                            ? $task->subtasks->firstWhere('assignee_id', auth()->id())
                            : null;
                    @endphp
                    <a href="{{ route('tasks.edit', $task) }}" class="block px-3 py-2 hover:bg-gray-50">
                        <p class="text-[12px] font-medium text-[#1F2937]">{{ $task->title }}</p>
                        <p class="mt-0.5 text-[10px] text-gray-500">
                            {{ $task->project->name }}
                            @if ($task->assignee) &middot; <x-avatar :user="$task->assignee" size="16px" class="align-middle" /> {{ $task->assignee->name }} @endif
                            @if ($task->due_date) &middot; {{ $task->due_date->format('M j') }} @endif
                            &middot; {{ $task->priority->label() }} &middot; {{ $task->status->label() }}
                        </p>
                        @if ($myViaSubtask)
                            <p class="mt-0.5 text-[10px] font-medium text-[#1D9E75]">Your subtask: {{ $myViaSubtask->title }}</p>
                        @endif
                    </a>
                @empty
                    <p class="px-3 py-4 text-center text-[11px] text-gray-400">No tasks</p>
                @endforelse
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
