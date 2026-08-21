@extends('layouts.authenticated')

@section('title', 'Calendar — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Calendar</h1>
    <p class="mt-1 text-[11px] text-gray-500">Tasks positioned by due date, grouped by project (color-coded). Tasks with no due date aren't shown here.</p>

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">{{ $emptyMessage ?? "You don't have access to any companies yet." }}</p>
    @else
        <div class="mt-4 flex gap-1 border-b border-gray-200">
            @foreach ($organizations as $tab)
                @php $isActiveTab = $organization && $organization->id === $tab->id @endphp
                <a href="{{ route('calendar', $tab) }}"
                   style="border-color: {{ $tab->accent_color }}"
                   class="border-b-2 px-4 py-2 text-[12px] {{ $isActiveTab ? 'font-medium text-[#1F2937]' : 'text-gray-500 hover:text-gray-700' }}">
                    {{ $tab->name }}
                </a>
            @endforeach
        </div>

        <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white">
            @if (empty($ganttTasks))
                <p class="py-10 text-center text-[12px] text-gray-500">No tasks with a due date in {{ $organization->name }}.</p>
            @else
                <div id="gantt-container" data-tasks='@json($ganttTasks)'></div>
            @endif
        </div>
    @endif
</div>
@endsection
