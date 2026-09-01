@extends('layouts.authenticated')

@section('title', 'Calendar — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Calendar</h1>
        @if ($organization && $canCreate)
            <a href="{{ route('tasks.create', $defaultProject) }}"
               class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                + Add task
            </a>
        @endif
    </div>

    <p class="mt-1 text-[11px] text-gray-500">Tasks positioned by due date. Tasks with no due date aren't shown here. Click a task to open it.</p>

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">{{ $emptyMessage ?? "You don't have access to any companies yet." }}</p>
    @else
        @php
            // The view toggle keeps the current anchor date (so switching
            // Month/Week doesn't jump the calendar back to today); prev/
            // Today/next keep the current view unchanged and only move the
            // anchor date.
            $viewLinkQuery = fn (string $targetView) => http_build_query(array_filter([
                'view' => $targetView,
                'date' => request('date'),
            ], fn ($value) => $value !== null && $value !== ''));

            $navLinkQuery = fn (string $date) => http_build_query(['view' => $view, 'date' => $date]);

            // Org-agnostic calendar state (view/date) carries across a
            // company-tab switch — unlike Tasks/Projects' filters, this
            // isn't a set of IDs scoped to the company being left.
            $tabQuery = http_build_query(array_filter([
                'view' => $view !== 'month' ? $view : null,
                'date' => request('date'),
            ], fn ($value) => $value !== null && $value !== ''));

            $dayHeaderLabels = collect($weeks[0])->map(fn ($day) => strtoupper($day['date']->format('D')));
        @endphp

        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('calendar', $organization) }}?{{ $navLinkQuery($prevDate) }}" aria-label="Previous"
                   class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] text-gray-600 hover:bg-gray-50 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    &larr;
                </a>
                <a href="{{ route('calendar', $organization) }}?{{ $navLinkQuery($todayDate) }}"
                   class="rounded-md border border-gray-300 px-3 py-1.5 text-[12px] text-gray-600 hover:bg-gray-50 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    Today
                </a>
                <a href="{{ route('calendar', $organization) }}?{{ $navLinkQuery($nextDate) }}" aria-label="Next"
                   class="rounded-md border border-gray-300 px-2 py-1.5 text-[12px] text-gray-600 hover:bg-gray-50 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    &rarr;
                </a>
                <span class="ml-2 text-[13px] font-medium text-[#1F2937]">{{ $rangeLabel }}</span>
            </div>

            <div class="inline-flex overflow-hidden rounded-md border border-gray-300">
                <a href="{{ route('calendar', $organization) }}?{{ $viewLinkQuery('month') }}"
                   class="px-3 py-1.5 text-[12px] font-medium {{ $view === 'month' ? 'bg-brand-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    Month
                </a>
                <a href="{{ route('calendar', $organization) }}?{{ $viewLinkQuery('week') }}"
                   class="border-l border-gray-300 px-3 py-1.5 text-[12px] font-medium {{ $view === 'week' ? 'bg-brand-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    Week
                </a>
            </div>
        </div>

        <x-company-tabs :organizations="$organizations" :active="$organization" route="calendar" :query="$tabQuery">
        @if ($view === 'week')
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                <div class="grid grid-cols-7">
                    @foreach ($weeks[0] as $day)
                        <div class="{{ ! $loop->last ? 'border-r border-gray-200' : '' }}">
                            <div class="border-b border-gray-200 px-2 py-2">
                                <div class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">{{ strtoupper($day['date']->format('D')) }}</div>
                                @if ($day['isToday'])
                                    <span class="mt-0.5 inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-brand-600 px-1.5 text-[13px] font-medium text-white">{{ $day['date']->day }}</span>
                                @else
                                    <div class="mt-0.5 text-[15px] font-medium text-gray-900">{{ $day['date']->day }}</div>
                                @endif
                            </div>
                            <div class="min-h-[280px] p-2">
                                @if ($canCreate)
                                    <a href="{{ route('tasks.create', $defaultProject) }}?due_date={{ $day['date']->toDateString() }}"
                                       class="mb-2 block text-[11px] text-gray-500 hover:text-gray-700">
                                        + Add task
                                    </a>
                                @endif
                                <div class="space-y-1">
                                    @foreach ($day['tasks'] as $task)
                                        <a href="{{ route('tasks.edit', $task) }}" title="{{ $task->title }}"
                                           class="block truncate rounded px-1.5 py-1 text-[11px] font-medium"
                                           style="background-color: {{ $task->priority->badgeBackground() }}; color: {{ $task->priority->badgeText() }};">
                                            {{ $task->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
                    @foreach ($dayHeaderLabels as $label)
                        <div class="px-2 py-2 text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">{{ $label }}</div>
                    @endforeach
                </div>
                @foreach ($weeks as $week)
                    <div class="grid grid-cols-7">
                        @foreach ($week as $day)
                            <div class="min-h-[110px] p-1.5
                                {{ ! $loop->last ? 'border-r border-gray-200' : '' }}
                                {{ ! $loop->parent->last ? 'border-b border-gray-200' : '' }}">
                                <div>
                                    @if ($day['isToday'])
                                        <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-full bg-brand-600 px-1.5 text-[11px] font-medium text-white">{{ $day['date']->day }}</span>
                                    @else
                                        <span class="text-[11px] {{ $day['isCurrentPeriod'] ? 'text-gray-700' : 'text-gray-400' }}">{{ $day['date']->day }}</span>
                                    @endif
                                </div>
                                <div class="mt-1 space-y-1">
                                    @foreach ($day['tasks'] as $task)
                                        <a href="{{ route('tasks.edit', $task) }}" title="{{ $task->title }}"
                                           class="block truncate rounded px-1.5 py-0.5 text-[10px] font-medium"
                                           style="background-color: {{ $task->priority->badgeBackground() }}; color: {{ $task->priority->badgeText() }};">
                                            {{ $task->title }}
                                        </a>
                                    @endforeach
                                </div>
                                @if ($canCreate)
                                    <a href="{{ route('tasks.create', $defaultProject) }}?due_date={{ $day['date']->toDateString() }}"
                                       class="mt-1 block text-[10px] text-gray-500 hover:text-gray-700">
                                        + Add task
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
        </x-company-tabs>
    @endif
</div>
@endsection
