@extends('layouts.authenticated')

@section('title', 'Calendar — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Calendar</h1>
    <p class="mt-1 text-[11px] text-gray-500">Tasks positioned by due date, grouped by project (color-coded). Tasks with no due date aren't shown here. Use + to show or hide a task's subtasks; click a bar to open its task.</p>

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">{{ $emptyMessage ?? "You don't have access to any companies yet." }}</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="calendar" />

        @if (! empty($ganttTasks))
            <div class="mt-4 flex items-center justify-end gap-2">
                <button type="button" id="calendar-prev" aria-label="Previous"
                        class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] text-gray-600 hover:bg-gray-50 focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    &larr;
                </button>
                <button type="button" id="calendar-next" aria-label="Next"
                        class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] text-gray-600 hover:bg-gray-50 focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    &rarr;
                </button>
                <select id="calendar-view-select" class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
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
                    <div id="gantt-task-list" class="w-[220px] shrink-0 overflow-hidden border-r border-gray-200 bg-white"></div>
                    <div id="gantt-container" class="min-w-0 flex-1" data-tasks='@json($ganttTasks)' data-subtasks='@json($subtasksByTask)'></div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
