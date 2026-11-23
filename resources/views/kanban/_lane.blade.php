{{--
    SOL-0025: one project's lane on the Kanban board. Closed, it is one
    row: the + button, the project name, a "Closed" tag for a closed
    project, "N tasks", and the lane's count per status. Opened, the body
    shows one cell per status, filled from kanban.project the first time
    (the board script); the header counts are hidden (invisible, so the
    row keeps its alignment) while it's open.

    Expects: $lane (id, name, closed, totals, total, older),
    $organization, $showAllCompleted. kanban.blade.php also renders it
    once inside <template id="kanban-lane-template"> with the id
    __PROJECT__ and an empty name, so the live poll can add a lane; the
    script swaps the id in and sets the name with textContent.

    Every line a cell can need ("No tasks", Loading…, the error, Load
    more, the older line) is always in the page, hidden when it doesn't
    apply, so the script only shows and hides them.
--}}
@php
    $completedAll = $showAllCompleted ? ['completed' => 'all'] : [];
    $laneGrid = 'grid grid-cols-[200px_repeat(4,minmax(0,1fr))] gap-2';
@endphp
<section class="kanban-lane rounded-lg border border-gray-200 bg-white"
         data-project-id="{{ $lane['id'] }}" data-name="{{ $lane['name'] }}" data-loaded="0"
         data-url="{{ route('kanban.project', [$organization, $lane['id']] + $completedAll) }}">
    <div class="kanban-lane-header {{ $laneGrid }} cursor-pointer items-center border-gray-200 px-2 py-2.5">
        <div class="flex min-w-0 items-start gap-2">
            <button type="button" class="kanban-lane-toggle flex h-5 w-5 shrink-0 items-center justify-center rounded border border-gray-300 bg-white text-[12px] leading-none text-gray-600 hover:bg-gray-50"
                    aria-expanded="false" aria-controls="kanban-lane-body-{{ $lane['id'] }}" aria-label="Open {{ $lane['name'] }}">+</button>
            <div class="min-w-0">
                <div class="flex min-w-0 items-center gap-1.5">
                    <span class="kanban-lane-name truncate text-[12px] font-medium text-[#1F2937]" title="{{ $lane['name'] }}">{{ $lane['name'] }}</span>
                    <span class="kanban-lane-closed shrink-0 rounded-sm bg-gray-100 px-1.5 py-px text-[9px] font-medium text-gray-500" @unless ($lane['closed']) hidden @endunless>Closed</span>
                </div>
                <p class="kanban-lane-total text-[10px] text-gray-500">{{ number_format($lane['total']) }} {{ \Illuminate\Support\Str::plural('task', $lane['total']) }}</p>
            </div>
        </div>
        @foreach (\App\Enums\TaskStatus::cases() as $status)
            <span class="kanban-lane-count text-center text-[11px] {{ $lane['totals'][$status->value] > 0 ? 'font-medium text-[#1F2937]' : 'text-gray-400' }}"
                  data-status="{{ $status->value }}">{{ number_format($lane['totals'][$status->value]) }}</span>
        @endforeach
    </div>
    <div id="kanban-lane-body-{{ $lane['id'] }}" class="kanban-lane-body" hidden>
        <div class="{{ $laneGrid }} p-2">
            <p class="px-1 pt-1 text-[10px] text-gray-400">Drag cards between columns to change status.</p>
            @foreach (\App\Enums\TaskStatus::cases() as $status)
                <div class="kanban-cell rounded-md bg-gray-100 p-1.5" data-status="{{ $status->value }}" data-total="{{ $lane['totals'][$status->value] }}">
                    <div class="kanban-cell-body min-h-[60px] space-y-2">
                        <p class="kanban-empty py-4 text-center text-[11px] text-gray-400" hidden></p>
                    </div>
                    <p class="kanban-cell-loading py-4 text-center text-[11px] text-gray-400" hidden>Loading…</p>
                    <p class="kanban-cell-error py-2 text-center text-[10px] text-[#A32D2D]" hidden>Couldn't load this project. Try again.</p>
                    {{-- task #72 phase 5: a cell shows its first 30 cards;
                         the rest arrive 30 at a time from kanban.column,
                         for this project only. --}}
                    <div class="kanban-load-more mt-2 border-t border-gray-200 pt-2 text-center"
                         data-url="{{ route('kanban.column', [$organization, $status->value, 'project' => $lane['id']] + $completedAll) }}"
                         data-offset="0" hidden>
                        <p class="kanban-more-count text-[10px] text-gray-500"></p>
                        <button type="button" class="kanban-load-more-btn mt-1 rounded-md border border-gray-300 bg-white px-3 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-wait disabled:opacity-60">
                            Load more
                        </button>
                        <p class="kanban-load-more-error mt-1 text-[10px] text-[#A32D2D]" hidden>Couldn't load more cards. Try again.</p>
                    </div>
                    {{-- task #72 phase 7: completed more than the window
                         ago; Show all shows them on the whole board. --}}
                    @if ($status === \App\Enums\TaskStatus::Completed && ! $showAllCompleted)
                        <p class="kanban-older pt-2 text-center text-[10px] text-gray-500"
                           data-show-all-url="{{ route('kanban', [$organization, 'completed' => 'all']) }}" @if ($lane['older'] === 0) hidden @endif>
                            @if ($lane['older'] > 0)
                                {{ number_format($lane['older']) }} older completed ·
                                <a href="{{ route('kanban', [$organization, 'completed' => 'all']) }}" class="text-brand-700 hover:underline">Show all</a>
                            @endif
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
