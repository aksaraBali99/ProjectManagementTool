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
        {{-- task #72 phase 10: shown when the board stops updating itself
             (session expired, lost access, or too many changes at once). --}}
        <p id="kanban-live-note" class="mb-2 text-[11px] text-gray-500" hidden>
            <a href="{{ url()->full() }}" class="text-brand-700 hover:underline">Reload to see updates</a>
        </p>
        {{-- task #72 phase 10: data-changes-url / data-cursor drive the poll
             in the script below; the URL carries ?completed=all in that
             mode, like Load more. --}}
        <div id="kanban-board" class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 md:snap-none md:overflow-visible"
             data-changes-url="{{ route('kanban.changes', [$organization] + ($showAllCompleted ? ['completed' => 'all'] : [])) }}"
             data-cursor="{{ $cursor }}">
            @foreach ($columns as $column)
                <div class="kanban-column w-[85vw] shrink-0 snap-center rounded-lg border border-gray-200 bg-white sm:w-[320px] md:min-w-0 md:flex-1 md:snap-align-none"
                     data-status="{{ $column['status']->value }}" data-total="{{ $column['total'] }}">
                    <div class="flex items-center justify-between rounded-t-lg border-b border-gray-200 px-3 py-2"
                         style="background-color: {{ $column['status']->badgeBackground() }}">
                        <span class="text-[10px] font-medium uppercase tracking-[0.06em]" style="color: {{ $column['status']->badgeText() }}">{{ $column['status']->label() }}</span>
                        <span class="kanban-column-total text-[10px]" style="color: {{ $column['status']->badgeText() }}">{{ number_format($column['total']) }}</span>
                    </div>
                    {{-- task #72 phase 10: "No tasks", Load more and the
                         "older" line are always in the page, hidden (and
                         empty) when they don't apply, so the poll can show
                         them when cards arrive or leave. --}}
                    <div class="kanban-column-body min-h-[80px] space-y-2 p-2">
                        @foreach ($column['tasks'] as $item)
                            @include('kanban._card', $item + ['eligibleAssignees' => $eligibleAssignees, 'returnTo' => $returnTo])
                        @endforeach
                        <p class="kanban-empty py-4 text-center text-[11px] text-gray-400" @if ($column['tasks']->isNotEmpty()) hidden @endif>{{ $column['tasks']->isEmpty() ? 'No tasks' : '' }}</p>
                    </div>
                    {{-- task #72 phase 5: a column shows its first 30 cards;
                         the rest arrive 30 at a time from kanban.column. --}}
                    <div class="kanban-load-more border-t border-gray-100 px-2 py-2 text-center"
                         data-url="{{ route('kanban.column', [$organization, $column['status']->value] + ($column['showAllCompleted'] ? ['completed' => 'all'] : [])) }}"
                         data-offset="{{ $column['tasks']->count() }}" @if ($column['remaining'] === 0) hidden @endif>
                        <p class="kanban-more-count text-[10px] text-gray-500">{{ $column['remaining'] > 0 ? number_format($column['remaining']).' more' : '' }}</p>
                        <button type="button" class="kanban-load-more-btn mt-1 rounded-md border border-gray-300 bg-white px-3 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-wait disabled:opacity-60">
                            Load more
                        </button>
                        <p class="kanban-load-more-error mt-1 text-[10px] text-[#A32D2D]" hidden>Couldn't load more cards. Try again.</p>
                    </div>
                    {{-- task #72 phase 7: the Completed column shows the
                         company's window by default; ?completed=all for
                         this visit only. --}}
                    @if ($column['showAllCompleted'])
                        <div class="kanban-completed-window border-t border-gray-100 px-2 py-2 text-center text-[10px] text-gray-500">
                            <a href="{{ route('kanban', $organization) }}" class="text-brand-700 hover:underline">Show recent only</a>
                        </div>
                    @elseif ($column['status'] === \App\Enums\TaskStatus::Completed)
                        <div class="kanban-older border-t border-gray-100 px-2 py-2 text-center text-[10px] text-gray-500 @if ($column['older'] > 0) kanban-completed-window @endif"
                             data-show-all-url="{{ route('kanban', [$organization, 'completed' => 'all']) }}" @if ($column['older'] === 0) hidden @endif>
                            @if ($column['older'] > 0)
                                {{ number_format($column['older']) }} older completed {{ \Illuminate\Support\Str::plural('task', $column['older']) }} ·
                                <a href="{{ route('kanban', [$organization, 'completed' => 'all']) }}" class="text-brand-700 hover:underline">Show all</a>
                            @endif
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

        function cardEl(taskId) {
            return board.querySelector('.kanban-card[data-task-id="' + taskId + '"]');
        }

        // The value the server last saved, not whatever the select shows
        // now: the option rendered `selected`, or the first one (Unassigned).
        function savedValue(select) {
            const option = select.querySelector('option[selected]') || select.options[0];
            return option ? option.value : '';
        }

        // A short line at the foot of a card: "Saving…" while your own
        // change is on its way, or the reason it failed.
        function setCardMessage(card, text, isError) {
            if (! card) return;
            let message = card.querySelector('.kanban-card-message');
            if (! text) {
                if (message) message.remove();
                return;
            }
            if (! message) {
                message = document.createElement('p');
                card.appendChild(message);
            }
            message.className = 'kanban-card-message mt-1 text-[10px] ' + (isError ? 'text-[#A32D2D]' : 'text-gray-500');
            message.textContent = text;
        }

        // task #72 phase 10: your own status and assignee changes no longer
        // reload the page. While the request runs the card is busy (the
        // poll leaves it alone) and says "Saving…". On success the busy
        // flag is cleared FIRST, then the board polls at once, so the poll
        // applies this very change: the card moves to its new place with
        // its new avatar, rendered by the server. On failure the select
        // goes back to the saved value and the card says why; a dragged
        // card never moved, so there is nothing else to undo.
        function saveCardChange(taskId, url, payload, select, fallbackMessage) {
            const id = String(taskId);
            const card = cardEl(id);
            busy.add(id);
            setCardMessage(card, 'Saving…', false);

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            })
                .then(function (response) {
                    if (response.ok) return;
                    // Surfaces the backend's actual message (an
                    // authorization failure, or the assignee rule's field
                    // error) instead of a generic string.
                    return response.json().catch(function () { return null; }).then(function (data) {
                        const fieldErrors = data && data.errors ? Object.values(data.errors)[0] : null;
                        throw new Error((Array.isArray(fieldErrors) && fieldErrors[0]) || (data && data.message) || fallbackMessage);
                    });
                })
                .then(function () {
                    busy.delete(id);
                    if (select) select.blur();
                    return poll().then(function () {
                        // Still here and unchanged (the poll failed, or
                        // nothing actually changed): don't leave it saying
                        // "Saving…".
                        const current = cardEl(id);
                        if (current && ! busy.has(id)) setCardMessage(current, null);
                    });
                })
                .catch(function (error) {
                    busy.delete(id);
                    if (select) {
                        select.value = savedValue(select);
                        select.blur();
                    }
                    setCardMessage(cardEl(id), error.message, true);
                    flushPending();
                });
        }

        function changeStatus(taskId, status, select) {
            const card = cardEl(taskId);
            const statusSelect = card ? card.querySelector('.kanban-status-select') : null;
            // Dropped back onto its own column: nothing to save.
            if (! select && statusSelect && savedValue(statusSelect) === status) return;

            saveCardChange(taskId, '/tasks/' + taskId + '/status', { status: status }, select, 'Failed to change status.');
        }

        function changeAssignee(taskId, assigneeId, select) {
            saveCardChange(taskId, '/tasks/' + taskId + '/assignee', { assignee_id: assigneeId || null }, select, 'Failed to change assignee.');
        }

        // task #72 phase 5: every listener is delegated on the board rather
        // than bound per card, so cards appended by Load more (or placed by
        // the poll) drag and use both dropdowns without any re-binding.
        function closestIn(event, selector) {
            return event.target instanceof Element ? event.target.closest(selector) : null;
        }

        board.addEventListener('dragstart', function (event) {
            const card = closestIn(event, '.kanban-card[draggable="true"]');
            if (! card) return;
            draggingId = card.dataset.taskId;
            event.dataTransfer.setData('text/plain', card.dataset.taskId);
        });

        board.addEventListener('dragend', function () {
            draggingId = null;
            flushPending();
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
            changeStatus(taskId, column.dataset.status, null);
        });

        board.addEventListener('change', function (event) {
            const select = event.target;
            if (! (select instanceof Element)) return;

            if (select.matches('.kanban-status-select')) {
                changeStatus(select.dataset.taskId, select.value, select);
            } else if (select.matches('.kanban-assignee-select')) {
                changeAssignee(select.dataset.taskId, select.value, select);
            }
        });

        // Leaving a dropdown may free a card the poll skipped.
        board.addEventListener('focusout', function () {
            setTimeout(flushPending, 0);
        });

        board.addEventListener('click', function (event) {
            const button = closestIn(event, '.kanban-load-more-btn');
            if (button) loadMore(button.closest('.kanban-load-more'));
        });

        // task #72 phase 5: the next 30 cards of one column. The offset is
        // the number of cards this column shows (phase 10 keeps it equal
        // to that as the poll adds and removes cards, so a card moving in
        // or out above it shifts the server's list and the offset
        // together). A card already on the board is skipped, never shown
        // twice.
        function loadMore(container) {
            const button = container.querySelector('.kanban-load-more-btn');
            const error = container.querySelector('.kanban-load-more-error');
            if (button.disabled) return;

            button.disabled = true;
            error.hidden = true;

            // The URL may already carry ?completed=all (task #72 phase 7).
            const offset = Number(container.dataset.offset);
            const url = new URL(container.dataset.url, window.location.origin);
            url.searchParams.set('offset', String(offset));
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
                    const column = container.closest('.kanban-column');
                    const body = column.querySelector('.kanban-column-body');
                    const template = document.createElement('template');
                    template.innerHTML = data.html;

                    const cards = Array.from(template.content.querySelectorAll('.kanban-card'));
                    cards.forEach(function (card) {
                        if (cardEl(card.dataset.taskId)) return;
                        body.insertBefore(card, body.querySelector('.kanban-empty'));
                    });

                    // The column's total as of this batch: what came before
                    // it, the batch, and what is left after it.
                    column.dataset.total = String(offset + cards.length + data.remaining);
                    refreshColumn(column);
                })
                .catch(function () {
                    // The button stays, so the next click retries.
                    error.hidden = false;
                })
                .finally(function () {
                    button.disabled = false;
                });
        }

        // ---------------------------------------------------------------
        // task #72 phase 10: live updates. While the tab is visible, poll
        // kanban.changes every 15 s for what changed since the last poll,
        // and move, update, add or remove cards in place.
        // ---------------------------------------------------------------
        const POLL_INTERVAL = 15000;
        const changesUrl = board.dataset.changesUrl;
        let cursor = board.dataset.cursor;
        let timer = null;
        let current = null;
        let queued = null;
        let stopped = false;
        let draggingId = null;
        // Task ids with your own request in flight.
        const busy = new Set();
        // Updates held back for a busy card: id -> card data, or
        // { removed: true }. Applied when the card is free again.
        const pending = new Map();

        // Never move a card under the user: one being dragged, one whose
        // status or assignee dropdown has focus, or one being saved.
        function isBusy(taskId) {
            const id = String(taskId);
            if (busy.has(id) || draggingId === id) return true;
            const card = cardEl(id);
            const focused = document.activeElement;

            return !! (card && focused && card.contains(focused) && focused.matches('select'));
        }

        function formatNumber(number) {
            return number.toLocaleString('en-US');
        }

        // Header total, "No tasks", "N more" and the Load more offset, from
        // the column's total and the cards it shows.
        function refreshColumn(column) {
            const shown = column.querySelectorAll('.kanban-card').length;
            const total = Number(column.dataset.total);
            const remaining = Math.max(0, total - shown);

            column.querySelector('.kanban-column-total').textContent = formatNumber(total);

            const empty = column.querySelector('.kanban-empty');
            empty.hidden = shown > 0;
            empty.textContent = shown > 0 ? '' : 'No tasks';

            const more = column.querySelector('.kanban-load-more');
            more.dataset.offset = String(shown);
            more.hidden = remaining === 0;
            more.querySelector('.kanban-more-count').textContent = remaining > 0 ? formatNumber(remaining) + ' more' : '';
        }

        function setOlder(count) {
            const older = board.querySelector('.kanban-older');
            if (! older) return;

            older.hidden = count === 0;
            if (count === 0) {
                older.replaceChildren();
                return;
            }

            const link = document.createElement('a');
            link.href = older.dataset.showAllUrl;
            link.className = 'text-brand-700 hover:underline';
            link.textContent = 'Show all';
            older.replaceChildren(
                document.createTextNode(formatNumber(count) + ' older completed ' + (count === 1 ? 'task' : 'tasks') + ' · '),
                link,
            );
        }

        function toCardElement(change) {
            const template = document.createElement('template');
            template.innerHTML = change.html.trim();

            return template.content.firstElementChild;
        }

        // Apply changed cards and removed ids. A changed card always
        // replaces the one shown (two saves in the same second share an
        // updated_at, so nothing is skipped as "already seen"). It goes in
        // before the first card with a larger sort key; with no larger
        // card it is appended only if the column shows everything —
        // otherwise it belongs further down and will come with Load more,
        // and only "N more" changes.
        function applyChanges(cards, removed) {
            const ready = [];

            removed.forEach(function (taskId) {
                const id = String(taskId);
                if (isBusy(id)) {
                    pending.set(id, { removed: true });
                    return;
                }
                pending.delete(id);
                const card = cardEl(id);
                if (card) card.remove();
            });

            cards.forEach(function (change) {
                const id = String(change.id);
                if (isBusy(id)) {
                    pending.set(id, change);
                    return;
                }
                pending.delete(id);
                const card = cardEl(id);
                if (card) card.remove();
                ready.push(change);
            });

            board.querySelectorAll('.kanban-column').forEach(function (column) {
                const body = column.querySelector('.kanban-column-body');
                const incoming = ready
                    .filter(function (change) { return change.status === column.dataset.status; })
                    .map(toCardElement)
                    .filter(Boolean)
                    .sort(function (a, b) { return a.dataset.sortKey < b.dataset.sortKey ? -1 : 1; });
                const shown = body.querySelectorAll('.kanban-card').length;
                const showsAll = Number(column.dataset.total) <= shown + incoming.length;

                incoming.forEach(function (card) {
                    const after = Array.from(body.querySelectorAll('.kanban-card')).find(function (other) {
                        return other.dataset.sortKey > card.dataset.sortKey;
                    });
                    if (after) {
                        body.insertBefore(card, after);
                    } else if (showsAll) {
                        body.insertBefore(card, body.querySelector('.kanban-empty'));
                    }
                });

                refreshColumn(column);
            });
        }

        // Updates held back for cards that are free now.
        function flushPending() {
            const cards = [];
            const removed = [];
            pending.forEach(function (change, id) {
                if (isBusy(id)) return;
                if (change.removed) {
                    removed.push(id);
                } else {
                    cards.push(change);
                }
            });
            if (cards.length || removed.length) applyChanges(cards, removed);
        }

        function stop() {
            stopped = true;
            clearInterval(timer);
            timer = null;
            document.getElementById('kanban-live-note').hidden = false;
        }

        function pollOnce() {
            const url = new URL(changesUrl, window.location.origin);
            url.searchParams.set('since', cursor);

            return fetch(url.toString(), {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
            })
                .then(function (response) {
                    // Signed out (a redirect to the login page, or 401 for
                    // a JSON request), session token expired (419), or no
                    // longer allowed on this board (403/404): stop, and say
                    // so.
                    if (response.redirected || [401, 403, 404, 419].includes(response.status)) {
                        stop();
                        return null;
                    }
                    if (! response.ok) throw new Error('HTTP ' + response.status);

                    return response.json();
                })
                .then(function (data) {
                    if (! data) return;
                    if (data.stale) {
                        stop();
                        return;
                    }

                    // Advanced only after a successful poll, so a failed
                    // one loses nothing.
                    cursor = String(data.cursor);
                    board.querySelectorAll('.kanban-column').forEach(function (column) {
                        if (column.dataset.status in data.totals) {
                            column.dataset.total = String(data.totals[column.dataset.status]);
                        }
                    });
                    setOlder(data.older);
                    applyChanges(data.cards, data.removed);
                    flushPending();
                })
                .catch(function () {
                    // Quiet: the next tick tries again from the same cursor.
                });
        }

        // One request at a time. A poll asked for while one is running runs
        // once right after it (and callers get that one's promise), so a
        // change saved mid-poll is never missed.
        function poll() {
            if (stopped) return Promise.resolve();
            if (current) {
                if (! queued) {
                    queued = current.then(function () {
                        queued = null;
                        return poll();
                    });
                }
                return queued;
            }
            current = pollOnce().finally(function () {
                current = null;
            });

            return current;
        }

        function startTimer() {
            clearInterval(timer);
            timer = stopped ? null : setInterval(poll, POLL_INTERVAL);
        }

        // Hidden tab: no requests. Visible again: poll at once, then every
        // 15 s.
        document.addEventListener('visibilitychange', function () {
            if (stopped) return;
            if (document.visibilityState === 'visible') {
                poll();
                startTimer();
            } else {
                clearInterval(timer);
                timer = null;
            }
        });

        if (document.visibilityState === 'visible') startTimer();
    })();
</script>
@endsection
