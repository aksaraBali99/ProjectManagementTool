@extends('layouts.authenticated')

@section('title', 'Analytics — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Founder Analytics</h1>
    <p class="mt-1 text-[11px] text-gray-500">Cross-company overview. Deactivated tasks and inactive companies are excluded.</p>

    <div class="mt-4 inline-flex flex-col rounded-[10px] border border-gray-200 bg-white px-4 py-3">
        <span class="text-[10px] font-normal text-gray-500">Overdue tasks</span>
        <span class="mt-1 text-[18px] font-medium text-[#A32D2D]">{{ $overdueCount }}</span>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-[10px] border border-gray-200 bg-white p-3">
            <h2 class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Completion rate per company</h2>
            @if ($completionByCompany->isEmpty())
                <p class="mt-6 text-center text-[12px] text-gray-500">No active companies.</p>
            @else
                <div class="mt-2 h-64"><canvas
                    data-chart-type="bar"
                    data-chart-labels='@json($completionByCompany->pluck('label'))'
                    data-chart-values='@json($completionByCompany->pluck('rate'))'
                    data-chart-suffix="%"
                ></canvas></div>
            @endif
        </div>

        <div class="rounded-[10px] border border-gray-200 bg-white p-3">
            <h2 class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Tasks by status</h2>
            @if ($statusCounts->sum('count') === 0)
                <p class="mt-6 text-center text-[12px] text-gray-500">No tasks yet.</p>
            @else
                <div class="mt-2 h-64"><canvas
                    data-chart-type="doughnut"
                    data-chart-labels='@json($statusCounts->pluck('label'))'
                    data-chart-values='@json($statusCounts->pluck('count'))'
                ></canvas></div>
            @endif
        </div>

        <div class="rounded-[10px] border border-gray-200 bg-white p-3">
            <h2 class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Tasks by priority</h2>
            @if ($priorityCounts->sum('count') === 0)
                <p class="mt-6 text-center text-[12px] text-gray-500">No tasks yet.</p>
            @else
                <div class="mt-2 h-64"><canvas
                    data-chart-type="doughnut"
                    data-chart-labels='@json($priorityCounts->pluck('label'))'
                    data-chart-values='@json($priorityCounts->pluck('count'))'
                ></canvas></div>
            @endif
        </div>

        <div class="rounded-[10px] border border-gray-200 bg-white p-3">
            <h2 class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Staff workload (open tasks)</h2>
            @if ($staffWorkload->isEmpty())
                <p class="mt-6 text-center text-[12px] text-gray-500">No open tasks assigned to staff.</p>
            @else
                <div class="mt-2 h-64"><canvas
                    data-chart-type="bar"
                    data-chart-horizontal="1"
                    data-chart-labels='@json($staffWorkload->pluck('label'))'
                    data-chart-values='@json($staffWorkload->pluck('count'))'
                ></canvas></div>
            @endif
        </div>
    </div>
</div>
@endsection
