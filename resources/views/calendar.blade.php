@extends('layouts.authenticated')

@section('title', 'Calendar — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Calendar</h1>
    <p class="mt-1 text-[11px] text-gray-500">Tasks positioned by due date, grouped by project (color-coded). Tasks with no due date aren't shown here. Use + to show or hide a task's subtasks; click a bar to open its task. Drag the column divider to resize the task list, or scroll it to read a long name.</p>

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">{{ $emptyMessage ?? "You don't have access to any companies yet." }}</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="calendar">
        @if (! empty($ganttTasks))
            <div class="flex items-center justify-end gap-2">
                <button type="button" id="calendar-prev" aria-label="Previous"
                        class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] text-gray-600 hover:bg-gray-50 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    &larr;
                </button>
                <button type="button" id="calendar-next" aria-label="Next"
                        class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] text-gray-600 hover:bg-gray-50 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    &rarr;
                </button>
                <select id="calendar-view-select" class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    <option value="Week" selected>Week</option>
                    <option value="Month">Month</option>
                </select>
            </div>
        @endif

        <div class="mt-2 overflow-hidden rounded-lg border border-gray-200 bg-white">
            @if (empty($ganttTasks))
                <p class="py-10 text-center text-[12px] text-gray-500">No tasks with a due date in {{ $organization->name }}.</p>
            @else
                <div class="flex">
                    <div id="gantt-task-list" class="shrink-0 overflow-x-auto overflow-y-hidden bg-white"></div>
                    <div id="gantt-task-list-resizer" role="separator" aria-orientation="vertical" aria-label="Resize task list column"
                         class="group relative w-1 shrink-0 touch-none cursor-col-resize border-x border-gray-200 bg-gray-50 hover:bg-brand-600/20">
                        <span class="pointer-events-none absolute left-1/2 top-1/2 z-10 flex h-8 w-4 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-gray-300 bg-white text-gray-400 shadow-sm group-hover:border-brand-600 group-hover:text-brand-600">
                            <svg width="8" height="14" viewBox="0 0 8 14" fill="currentColor" aria-hidden="true">
                                <circle cx="2" cy="2" r="1.3" />
                                <circle cx="6" cy="2" r="1.3" />
                                <circle cx="2" cy="7" r="1.3" />
                                <circle cx="6" cy="7" r="1.3" />
                                <circle cx="2" cy="12" r="1.3" />
                                <circle cx="6" cy="12" r="1.3" />
                            </svg>
                        </span>
                    </div>
                    <div id="gantt-container" class="min-w-0 flex-1" data-tasks='@json($ganttTasks)' data-subtasks='@json($subtasksByTask)'></div>
                </div>
            @endif
        </div>
        </x-company-tabs>
    @endif
</div>
@endsection
