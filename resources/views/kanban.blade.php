@extends('layouts.authenticated')

@section('title', 'Kanban — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Kanban</h1>
        @if ($organization && $canCreate)
            <a href="{{ route('tasks.create', ['project' => $organization->projects()->orderBy('name')->first(), 'return_to' => url()->full(), 'return_label' => 'Kanban']) }}"
               class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                + Add task
            </a>
        @endif
    </div>

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">{{ $emptyMessage ?? "You don't have access to any companies yet." }}</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="kanban">
        {{-- Below md, columns become a horizontally scroll-snapping row
             (each column ~85% of the viewport, so the next one peeks in as
             a scroll affordance) rather than squeezing N columns into
             unusable slivers. At md and up this reverts to equal-width
             columns side by side via flex-1, same visual result as the
             old fixed-N-column grid but without needing a per-count inline
             style to get there. --}}
        <div id="kanban-board" class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 md:snap-none md:overflow-visible">
            @foreach ($columns as $column)
                <div class="kanban-column w-[85vw] shrink-0 snap-center rounded-lg border border-gray-200 bg-white sm:w-[320px] md:min-w-0 md:flex-1 md:snap-align-none"
                     data-status="{{ $column['status']->value }}">
                    <div class="flex items-center justify-between rounded-t-lg border-b border-gray-200 px-3 py-2"
                         style="background-color: {{ $column['status']->badgeBackground() }}">
                        <span class="text-[10px] font-medium uppercase tracking-[0.06em]" style="color: {{ $column['status']->badgeText() }}">{{ $column['status']->label() }}</span>
                        <span class="text-[10px]" style="color: {{ $column['status']->badgeText() }}">{{ number_format($column['total']) }}</span>
                    </div>
                    <div class="kanban-column-body min-h-[80px] space-y-2 p-2">
                        @forelse ($column['tasks'] as $item)
                            @include('kanban._card', $item + ['eligibleAssignees' => $eligibleAssignees, 'returnTo' => $returnTo])
                        @empty
                            <p class="py-4 text-center text-[11px] text-gray-400">No tasks</p>
                        @endforelse
                    </div>
                    {{-- task #72 phase 5: a column shows its first 30 cards;
                         the rest arrive 30 at a time from kanban.column. --}}
                    @if ($column['remaining'] > 0)
                        <div class="kanban-load-more border-t border-gray-100 px-2 py-2 text-center"
                             data-url="{{ route('kanban.column', [$organization, $column['status']->value] + ($column['showAllCompleted'] ? ['completed' => 'all'] : [])) }}"
                             data-offset="{{ $column['tasks']->count() }}">
                            <p class="kanban-more-count text-[10px] text-gray-500">{{ number_format($column['remaining']) }} more</p>
                            <button type="button" class="kanban-load-more-btn mt-1 rounded-md border border-gray-300 bg-white px-3 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-wait disabled:opacity-60">
                                Load more
                            </button>
                            <p class="kanban-load-more-error mt-1 text-[10px] text-[#A32D2D]" hidden>Couldn't load more cards. Try again.</p>
                        </div>
                    @endif
                    {{-- task #72 phase 7: the Completed column shows the
                         company's window by default; ?completed=all for
                         this visit only. --}}
                    @if ($column['showAllCompleted'])
                        <div class="kanban-completed-window border-t border-gray-100 px-2 py-2 text-center text-[10px] text-gray-500">
                            <a href="{{ route('kanban', $organization) }}" class="text-brand-700 hover:underline">Show recent only</a>
                        </div>
                    @elseif ($column['older'] > 0)
                        <div class="kanban-completed-window border-t border-gray-100 px-2 py-2 text-center text-[10px] text-gray-500">
                            {{ number_format($column['older']) }} older completed {{ \Illuminate\Support\Str::plural('task', $column['older']) }} ·
                            <a href="{{ route('kanban', [$organization, 'completed' => 'all']) }}" class="text-brand-700 hover:underline">Show all</a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        </x-company-tabs>
    @endif
</div>

<script>
    (function () {
        const board = document.getElementById('kanban-board');
        if (! board) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function changeStatus(taskId, status) {
            fetch('/tasks/' + taskId + '/status', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ status: status }),
            })
                .then(function (response) {
                    if (response.ok) {
                        window.location.reload();
                        return;
                    }
                    // Surfaces the backend's actual message (e.g. an
                    // authorization failure) instead of a generic string,
                    // falling back to that generic string only when the
                    // response carries no message of its own.
                    return response.json().catch(function () { return null; }).then(function (data) {
                        throw new Error((data && data.message) || 'Failed to change status.');
                    });
                })
                .catch(function (error) {
                    alert(error.message);
                    window.location.reload();
                });
        }

        // task #72 phase 5: every listener is delegated on the board rather
        // than bound per card, so cards appended by Load more drag and use
        // both dropdowns without any re-binding.
        function closestIn(event, selector) {
            return event.target instanceof Element ? event.target.closest(selector) : null;
        }

        board.addEventListener('dragstart', function (event) {
            const card = closestIn(event, '.kanban-card[draggable="true"]');
            if (! card) return;
            event.dataTransfer.setData('text/plain', card.dataset.taskId);
        });

        board.addEventListener('dragover', function (event) {
            if (closestIn(event, '.kanban-column')) event.preventDefault();
        });

        board.addEventListener('drop', function (event) {
            const column = closestIn(event, '.kanban-column');
            if (! column) return;
            event.preventDefault();
            const taskId = event.dataTransfer.getData('text/plain');
            if (! taskId) return;
            changeStatus(taskId, column.dataset.status);
        });

        board.addEventListener('change', function (event) {
            const select = event.target;
            if (! (select instanceof Element)) return;

            if (select.matches('.kanban-status-select')) {
                changeStatus(select.dataset.taskId, select.value);
            } else if (select.matches('.kanban-assignee-select')) {
                changeAssignee(select.dataset.taskId, select.value);
            }
        });

        board.addEventListener('click', function (event) {
            const button = closestIn(event, '.kanban-load-more-btn');
            if (button) loadMore(button.closest('.kanban-load-more'));
        });

        // task #72 phase 5: the next 30 cards of one column. The offset is
        // how many cards the server has sent this column so far; a card
        // already on the board (it moved here from another column since
        // the page loaded) is skipped, never shown twice.
        function loadMore(container) {
            const button = container.querySelector('.kanban-load-more-btn');
            const error = container.querySelector('.kanban-load-more-error');
            if (button.disabled) return;

            button.disabled = true;
            error.hidden = true;

            // The URL may already carry ?completed=all (task #72 phase 7).
            const url = new URL(container.dataset.url, window.location.origin);
            url.searchParams.set('offset', container.dataset.offset);
            fetch(url.toString(), {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
            })
                .then(function (response) {
                    if (response.redirected) throw new Error('redirected to ' + response.url);
                    if (! response.ok) throw new Error('HTTP ' + response.status);

                    return response.json();
                })
                .then(function (data) {
                    const body = container.closest('.kanban-column').querySelector('.kanban-column-body');
                    const template = document.createElement('template');
                    template.innerHTML = data.html;

                    const cards = Array.from(template.content.querySelectorAll('.kanban-card'));
                    cards.forEach(function (card) {
                        if (board.querySelector('.kanban-card[data-task-id="' + card.dataset.taskId + '"]')) return;
                        body.appendChild(card);
                    });

                    container.dataset.offset = String(Number(container.dataset.offset) + cards.length);

                    if (data.remaining > 0) {
                        container.querySelector('.kanban-more-count').textContent = data.remaining.toLocaleString('en-US') + ' more';
                    } else {
                        container.remove();
                    }
                })
                .catch(function () {
                    // The button stays, so the next click retries.
                    error.hidden = false;
                })
                .finally(function () {
                    button.disabled = false;
                });
        }

        function changeAssignee(taskId, assigneeId) {
            fetch('/tasks/' + taskId + '/assignee', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ assignee_id: assigneeId || null }),
            })
                .then(function (response) {
                    if (response.ok) {
                        // Full reload, same as changeStatus() - this is what
                        // keeps the card's avatar in sync with whoever is
                        // now actually assigned, without needing to
                        // duplicate avatar-rendering logic in JS.
                        window.location.reload();
                        return;
                    }
                    return response.json().catch(function () { return null; }).then(function (data) {
                        const fieldErrors = data && data.errors ? Object.values(data.errors)[0] : null;
                        throw new Error((Array.isArray(fieldErrors) && fieldErrors[0]) || (data && data.message) || 'Failed to change assignee.');
                    });
                })
                .catch(function (error) {
                    alert(error.message);
                    window.location.reload();
                });
        }
    })();
</script>
@endsection
