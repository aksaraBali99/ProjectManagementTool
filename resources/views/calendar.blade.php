@extends('layouts.authenticated')

@section('title', 'Calendar — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Calendar</h1>
    <p class="mt-1 text-[11px] text-gray-500">Tasks positioned by due date, grouped by project (color-coded). Tasks with no due date aren't shown here.</p>

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">{{ $emptyMessage ?? "You don't have access to any companies yet." }}</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="calendar" />

        @if (! empty($ganttTasks))
            <div class="mt-4 flex justify-end">
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
                <div id="gantt-container" data-tasks='@json($ganttTasks)'></div>
            @endif
        </div>
    @endif
</div>
@endsection
