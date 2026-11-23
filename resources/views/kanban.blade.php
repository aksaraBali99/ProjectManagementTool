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
        {{-- task #72 phase 10: shown when the board stops updating itself
             (session expired, lost access, or too many changes at once). --}}
        <p id="kanban-live-note" class="mb-2 text-[11px] text-gray-500" hidden>
            <a href="{{ url()->full() }}" class="text-brand-700 hover:underline">Reload to see updates</a>
        </p>
        {{-- SOL-0025 phase 2: the completed-window lines on the left, and
             Expand all / Collapse all on the right (hidden with no lanes;
             the script keeps them current). --}}
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
        <div class="min-w-0">
        {{-- task #72 phase 7: ?completed=all shows every completed task,
             for this visit only. --}}
        @if ($showAllCompleted)
            <p class="kanban-completed-window text-[11px] text-gray-500">
                Showing all completed tasks ·
                <a href="{{ route('kanban', $organization) }}" class="text-brand-700 hover:underline">Show recent only</a>
            </p>
        @else
            {{-- SOL-0025: a project whose visible tasks were all completed
                 before the window has no lane, so no lane's older line
                 reaches them; this one does. The poll keeps it current. --}}
            <p id="kanban-hidden-older" class="text-[11px] text-gray-500"
               data-show-all-url="{{ route('kanban', [$organization, 'completed' => 'all']) }}" @if ($hiddenOlder === 0) hidden @endif>
                @if ($hiddenOlder > 0)
                    {{ number_format($hiddenOlder) }} older completed {{ \Illuminate\Support\Str::plural('task', $hiddenOlder) }} in projects not shown ·
                    <a href="{{ route('kanban', [$organization, 'completed' => 'all']) }}" class="text-brand-700 hover:underline">Show all</a>
                @endif
            </p>
        @endif
        </div>
        <div id="kanban-lane-actions" class="ml-auto" @if ($lanes->isEmpty()) hidden @endif>
            <div class="flex gap-1.5">
                <button type="button" id="kanban-expand-all" class="rounded-md border border-gray-300 bg-white px-3 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-default disabled:opacity-60"
                        @if (count($openLaneIds) === $lanes->count()) disabled @endif>Expand all</button>
                <button type="button" id="kanban-collapse-all" class="rounded-md border border-gray-300 bg-white px-3 py-1 text-[11px] font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-default disabled:opacity-60"
                        @if ($openLaneIds === [] && $savedLaneIds === []) disabled @endif>Collapse all</button>
            </div>
        </div>
        </div>
        {{-- SOL-0025: one collapsible lane per project, sorted by name,
             under one header row of the four statuses. Below xl the board
             keeps a min width and scrolls sideways inside the folder, so
             the page never does (the header row only sticks to the top of
             the page at xl and up, where nothing scrolls sideways).

             task #72 phase 10: data-changes-url / data-cursor drive the
             poll in the script below; the URL carries ?completed=all in
             that mode, like the lanes and Load more. --}}
        <div class="overflow-x-auto pb-1 xl:overflow-x-visible">
            <div id="kanban-board" class="min-w-[880px]"
                 data-changes-url="{{ route('kanban.changes', [$organization] + ($showAllCompleted ? ['completed' => 'all'] : [])) }}"
                 data-cursor="{{ $cursor }}"
                 {{-- SOL-0025 phase 2: the saved open lanes on the board now
                      (opened on load), every id saved for this company (so a
                      save keeps those whose lane is gone for now), and
                      where to save. --}}
                 data-open-lanes="{{ json_encode($openLaneIds) }}"
                 data-saved-lanes="{{ json_encode($savedLaneIds) }}"
                 data-open-lanes-url="{{ route('kanban.open-lanes', $organization) }}">
                <div class="sticky top-0 z-10 grid grid-cols-[200px_repeat(4,minmax(0,1fr))] gap-2 px-[9px] pb-2"
                     style="background-color: {{ $organization->badgeBackground() }}">
                    <div class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-3 py-2">
                        <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-600">Project</span>
                        <span id="kanban-project-count" class="text-[10px] text-gray-600">{{ number_format($lanes->count()) }}</span>
                    </div>
                    @foreach (\App\Enums\TaskStatus::cases() as $status)
                        <div class="kanban-column-header flex items-center justify-between rounded-md px-3 py-2"
                             style="background-color: {{ $status->badgeBackground() }}" data-status="{{ $status->value }}">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em]" style="color: {{ $status->badgeText() }}">{{ $status->label() }}</span>
                            <span class="kanban-column-total text-[10px]" style="color: {{ $status->badgeText() }}">{{ number_format($totals[$status->value]) }}</span>
                        </div>
                    @endforeach
                </div>
                <div id="kanban-lanes" class="space-y-2">
                    @foreach ($lanes as $lane)
                        @include('kanban._lane', ['lane' => $lane, 'organization' => $organization, 'showAllCompleted' => $showAllCompleted])
                    @endforeach
                </div>
                <p id="kanban-no-lanes" class="rounded-lg border border-gray-200 bg-white py-6 text-center text-[12px] text-gray-500" @if ($lanes->isNotEmpty()) hidden @endif>No tasks</p>
            </div>
        </div>
        {{-- SOL-0025: an empty lane, for a project that gains its first
             task while the page is open. --}}
        <template id="kanban-lane-template">
            @include('kanban._lane', [
                'lane' => [
                    'id' => '__PROJECT__',
                    'name' => '',
                    'closed' => false,
                    'totals' => collect(\App\Enums\TaskStatus::cases())->mapWithKeys(fn ($status) => [$status->value => 0])->all(),
                    'total' => 0,
                    'older' => 0,
                ],
                'organization' => $organization,
                'showAllCompleted' => $showAllCompleted,
            ])
        </template>
        </x-company-tabs>
    @endif
</div>

<script>
    (function () {
        const board = document.getElementById('kanban-board');
        if (! board) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const lanesEl = document.getElementById('kanban-lanes');
        const laneTemplate = document.getElementById('kanban-lane-template');
        const expandAllBtn = document.getElementById('kanban-expand-all');
        const collapseAllBtn = document.getElementById('kanban-collapse-all');
        const STATUSES = Array.from(board.querySelectorAll('.kanban-column-header')).map(function (header) {
            return header.dataset.status;
        });

        function cardEl(taskId) {
            return board.querySelector('.kanban-card[data-task-id="' + taskId + '"]');
        }

        function laneEl(projectId) {
            return lanesEl.querySelector('.kanban-lane[data-project-id="' + projectId + '"]');
        }

        function cellOf(lane, status) {
            return lane.querySelector('.kanban-cell[data-status="' + status + '"]');
        }

        // The value the server last saved, not whatever the select shows
        // now: the option rendered `selected`, or the first one (Unassigned).
        function savedValue(select) {
            const option = select.querySelector('option[selected]') || select.options[0];
            return option ? option.value : '';
        }

        // A short line at the foot of a card: "Saving…" while your own
        // change is on its way, or the reason it failed.
        //
        // With clearAfter (ms) the message removes itself after that long —
        // but only if it is still the message this call set. Each message
        // gets a new id, so a newer "Saving…" or error (same element, new
        // id) or a card the poll replaced (old element detached) is left
        // alone; nothing needs cancelling.
        let messageSeq = 0;

        function setCardMessage(card, text, isError, clearAfter) {
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
            message.dataset.messageId = String(++messageSeq);

            if (clearAfter) {
                const mine = message.dataset.messageId;
                setTimeout(function () {
                    if (message.isConnected && message.dataset.messageId === mine) message.remove();
                }, clearAfter);
            }
        }

        // How long an ordinary save error stays on its card.
        const ERROR_MESSAGE_MS = 8000;

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
                    // Signed out: CSRF token expired (419), 401 for a JSON
                    // request, or redirected to the login page. Checked
                    // before response.ok, since a followed redirect ends on
                    // a 200 login page that must not count as a save.
                    if (response.redirected || response.status === 401 || response.status === 419) {
                        const expired = new Error('Your session expired. Reload the page.');
                        expired.sessionExpired = true;
                        throw expired;
                    }
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
                    // A poll already running started before this save, so
                    // it may carry the card's old state: polls up to this
                    // one are ignored for this card (see applyChanges()).
                    savedAfterPoll.set(id, pollSeq);
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
                    if (error.sessionExpired) {
                        // Nothing else works until a reload: the message
                        // stays, the board stops polling and shows its
                        // "Reload to see updates" note, and held-back
                        // updates aren't applied (they would replace the
                        // card and wipe the message).
                        setCardMessage(cardEl(id), error.message, true);
                        stop();
                        return;
                    }
                    setCardMessage(cardEl(id), error.message, true, ERROR_MESSAGE_MS);
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

        // ---------------------------------------------------------------
        // SOL-0025: project lanes. Every lane starts closed; its cards
        // come from kanban.project the first time it opens. Closing only
        // hides the body: the cards stay in the page and keep getting
        // live updates, so reopening needs no request. openLane() is the
        // one entry point for opening a lane by its project id.
        // ---------------------------------------------------------------
        function setLaneOpen(lane, open) {
            lane.dataset.open = open ? '1' : '0';
            lane.querySelector('.kanban-lane-body').hidden = ! open;
            lane.querySelector('.kanban-lane-header').classList.toggle('border-b', open);
            lane.querySelectorAll('.kanban-lane-count').forEach(function (count) {
                count.classList.toggle('invisible', open);
            });
            const toggle = lane.querySelector('.kanban-lane-toggle');
            toggle.textContent = open ? '−' : '+';
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', (open ? 'Close ' : 'Open ') + lane.dataset.name);
            refreshLaneActions();
        }

        // SOL-0025 phase 2: Expand all / Collapse all, hidden with no lanes,
        // each disabled while it would change nothing. Collapse all also
        // clears saved lanes not on the page now, so it stays enabled
        // while there are any.
        function refreshLaneActions() {
            const lanes = lanesEl.querySelectorAll('.kanban-lane');
            const open = lanesEl.querySelectorAll('.kanban-lane[data-open="1"]').length;
            document.getElementById('kanban-lane-actions').hidden = lanes.length === 0;
            expandAllBtn.disabled = open === lanes.length;
            collapseAllBtn.disabled = open === 0 && savedOpen.size === 0;
        }

        function openLane(projectId) {
            const lane = laneEl(projectId);
            if (! lane) return;
            setLaneOpen(lane, true);
            if (lane.dataset.loaded !== '1') loadLane(lane);
        }

        function closeLane(projectId) {
            const lane = laneEl(projectId);
            if (! lane) return;
            dequeueLane(lane);
            setLaneOpen(lane, false);
        }

        // "Loading…", the error line, or neither, in every cell of a lane.
        function setLaneState(lane, state) {
            lane.querySelectorAll('.kanban-cell').forEach(function (cell) {
                cell.querySelector('.kanban-cell-loading').hidden = state !== 'loading';
                cell.querySelector('.kanban-cell-error').hidden = state !== 'error';
                if (state) {
                    cell.querySelector('.kanban-empty').hidden = true;
                    cell.querySelector('.kanban-load-more').hidden = true;
                }
            });
        }

        // A lane's first cards. On failure the lane stays unloaded, so the
        // next open tries again. Cards the poll brought for this lane while
        // it loaded were skipped (applyChanges() only fills loaded lanes),
        // so the next poll re-reads from the time of this response.
        //
        // SOL-0025 phase 2: loads queue up and run LANE_LOADS_AT_ONCE at a
        // time, in the order asked (top first on page load and Expand all).
        // A lane shows "Loading…" while it waits its turn. Closing a lane
        // still waiting takes it out of the queue (no request is sent);
        // only while its request runs does its header ignore clicks.
        const LANE_LOADS_AT_ONCE = 3;
        const laneQueue = [];
        let laneLoadsRunning = 0;

        function loadLane(lane) {
            if (lane.dataset.loading === '1') return;
            lane.dataset.loading = '1';
            lane.dataset.queued = '1';
            setLaneState(lane, 'loading');
            laneQueue.push(lane);
            runLaneQueue();
        }

        function dequeueLane(lane) {
            if (lane.dataset.queued !== '1') return;
            const index = laneQueue.indexOf(lane);
            if (index !== -1) laneQueue.splice(index, 1);
            delete lane.dataset.queued;
            delete lane.dataset.loading;
            setLaneState(lane, null);
        }

        function runLaneQueue() {
            while (laneLoadsRunning < LANE_LOADS_AT_ONCE && laneQueue.length > 0) {
                const lane = laneQueue.shift();
                delete lane.dataset.queued;
                if (! lane.isConnected) {
                    delete lane.dataset.loading;
                    continue;
                }
                laneLoadsRunning++;
                fetchLane(lane).finally(function () {
                    laneLoadsRunning--;
                    runLaneQueue();
                });
            }
        }

        function fetchLane(lane) {
            return fetch(lane.dataset.url, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
            })
                .then(function (response) {
                    if (response.redirected) throw new Error('redirected to ' + response.url);
                    if (! response.ok) throw new Error('HTTP ' + response.status);

                    return response.json();
                })
                .then(function (data) {
                    if (! lane.isConnected) return;
                    setLaneState(lane, null);
                    Object.keys(data.columns).forEach(function (status) {
                        const cell = cellOf(lane, status);
                        if (! cell) return;
                        const body = cell.querySelector('.kanban-cell-body');
                        const template = document.createElement('template');
                        template.innerHTML = data.columns[status].html;
                        Array.from(template.content.querySelectorAll('.kanban-card')).forEach(function (card) {
                            // Still shown in another lane (moved, and the
                            // poll hasn't caught up): this is the newer
                            // place, unless you're working on that card.
                            const id = card.dataset.taskId;
                            const existing = cardEl(id);
                            if (existing) {
                                // You're working on it there: it moves here
                                // once free (counted in this cell's total
                                // meanwhile), unless the poll already holds
                                // an update for it.
                                if (isBusy(id)) {
                                    if (! pending.has(id)) {
                                        pending.set(id, {
                                            change: {
                                                id: Number(id),
                                                status: status,
                                                project_id: Number(lane.dataset.projectId),
                                                sort_key: card.dataset.sortKey,
                                                html: card.outerHTML,
                                            },
                                            seq: pollSeq,
                                        });
                                    }
                                    return;
                                }
                                existing.remove();
                            }
                            body.insertBefore(card, body.querySelector('.kanban-empty'));
                        });
                        cell.dataset.total = String(data.columns[status].total);
                        refreshCell(cell);
                    });
                    setOlder(lane, data.older);
                    lane.dataset.loaded = '1';
                    // A poll that landed while this lane loaded skipped
                    // its cards, and may have moved the cursor past a
                    // change this response predates: the next poll
                    // re-reads from this response's own time.
                    rewindTo = rewindTo === null ? Number(data.cursor) : Math.min(rewindTo, Number(data.cursor));
                })
                .catch(function () {
                    if (lane.isConnected) setLaneState(lane, 'error');
                })
                .finally(function () {
                    delete lane.dataset.loading;
                });
        }

        // task #72 phase 5: every listener is delegated on the board rather
        // than bound per card, so cards from a lane, Load more or the poll
        // drag and use both dropdowns without any re-binding.
        function closestIn(event, selector) {
            return event.target instanceof Element ? event.target.closest(selector) : null;
        }

        // SOL-0025: a card moves between the cells of its own lane only;
        // another project's lane doesn't accept it. The target cell is
        // highlighted from dragover alone (cleared over anything else, on
        // drop and on dragend): some browsers send dragleave with no
        // relatedTarget while still over the cell, which made it flicker.
        let dragLane = null;
        let dropTarget = null;

        function setDropTarget(cell) {
            if (dropTarget === cell) return;
            if (dropTarget) {
                dropTarget.classList.remove('outline-2', 'outline-dashed', 'outline-brand-600', 'bg-brand-50');
                dropTarget.classList.add('bg-gray-100');
            }
            dropTarget = cell;
            if (cell) {
                cell.classList.remove('bg-gray-100');
                cell.classList.add('outline-2', 'outline-dashed', 'outline-brand-600', 'bg-brand-50');
            }
        }

        function sameLaneCell(event) {
            const cell = closestIn(event, '.kanban-cell');

            return cell && dragLane && cell.closest('.kanban-lane') === dragLane ? cell : null;
        }

        board.addEventListener('dragstart', function (event) {
            const card = closestIn(event, '.kanban-card[draggable="true"]');
            if (! card) return;
            draggingId = card.dataset.taskId;
            dragLane = card.closest('.kanban-lane');
            event.dataTransfer.setData('text/plain', card.dataset.taskId);
        });

        board.addEventListener('dragend', function () {
            draggingId = null;
            dragLane = null;
            setDropTarget(null);
            flushPending();
        });

        board.addEventListener('dragover', function (event) {
            const cell = sameLaneCell(event);
            setDropTarget(cell);
            if (cell) event.preventDefault();
        });

        board.addEventListener('drop', function (event) {
            const cell = sameLaneCell(event);
            setDropTarget(null);
            if (! cell) return;
            event.preventDefault();
            const taskId = event.dataTransfer.getData('text/plain');
            if (! taskId) return;
            changeStatus(taskId, cell.dataset.status, null);
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
            if (button) {
                loadMore(button.closest('.kanban-load-more'));
                return;
            }

            // The whole header row toggles its lane (its + / − button is
            // the keyboard's way in). A click while it loads is ignored.
            const header = closestIn(event, '.kanban-lane-header');
            if (! header) return;
            const lane = header.closest('.kanban-lane');
            // Its request is running: wait for it. (Still queued: it can
            // be closed.)
            if (lane.dataset.loading === '1' && lane.dataset.queued !== '1') return;
            if (lane.dataset.open === '1') {
                closeLane(lane.dataset.projectId);
                savedOpen.delete(lane.dataset.projectId);
            } else {
                openLane(lane.dataset.projectId);
                savedOpen.add(lane.dataset.projectId);
            }
            scheduleOpenLanesSave();
        });

        // SOL-0025 phase 2: Expand all opens every lane on the board (loads
        // through the queue above) and keeps the saved ids of lanes not on
        // the page now. Collapse all means "everything closed": it clears
        // the whole saved set for this company, those ids included. Cards
        // already loaded stay in the page either way.
        expandAllBtn.addEventListener('click', function () {
            lanesEl.querySelectorAll('.kanban-lane').forEach(function (lane) {
                if (lane.dataset.open !== '1') openLane(lane.dataset.projectId);
                savedOpen.add(lane.dataset.projectId);
            });
            scheduleOpenLanesSave();
        });

        collapseAllBtn.addEventListener('click', function () {
            lanesEl.querySelectorAll('.kanban-lane[data-open="1"]').forEach(function (lane) {
                closeLane(lane.dataset.projectId);
            });
            savedOpen.clear();
            scheduleOpenLanesSave();
        });

        // ---------------------------------------------------------------
        // SOL-0025 phase 2: the open lanes are remembered per user and
        // company. savedOpen holds every id saved as open, including lanes
        // not on the page right now (a lane that comes back reopens). Your
        // own opens and closes save the whole set, debounced so Expand all
        // or quick clicks send one request; opening the saved lanes on load
        // doesn't save. A save is a convenience: a failure is ignored and
        // never undoes anything (a dead session is the poll's to report).
        // ---------------------------------------------------------------
        const OPEN_LANES_SAVE_DELAY = 600;
        const openLanesUrl = board.dataset.openLanesUrl;
        const savedOpen = new Set(JSON.parse(board.dataset.savedLanes || '[]').map(String));
        let openLanesTimer = null;

        function scheduleOpenLanesSave() {
            clearTimeout(openLanesTimer);
            openLanesTimer = setTimeout(saveOpenLanes, OPEN_LANES_SAVE_DELAY);
        }

        function saveOpenLanes() {
            clearTimeout(openLanesTimer);
            openLanesTimer = null;
            // keepalive: still sent when the page is being left.
            fetch(openLanesUrl, {
                method: 'PUT',
                keepalive: true,
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ project_ids: openLanesPayload() }),
            }).catch(function () {});
        }

        // Lanes open on the page first (in lane order), then saved ids of
        // lanes not on it now: past the server's limit of 200, those are
        // the ones dropped, never a lane the user just opened.
        function openLanesPayload() {
            const onPage = Array.from(lanesEl.querySelectorAll('.kanban-lane')).map(function (lane) {
                return lane.dataset.projectId;
            });
            const shown = new Set(onPage);
            const open = onPage.filter(function (id) { return savedOpen.has(id); });
            const offPage = Array.from(savedOpen).filter(function (id) { return ! shown.has(id); });

            return open.concat(offPage).map(Number);
        }

        // Leaving within the debounce: send it now.
        window.addEventListener('pagehide', function () {
            if (openLanesTimer !== null) saveOpenLanes();
        });

        // task #72 phase 5: the next 30 cards of one cell (SOL-0025: of one
        // project; the URL names it). The offset is the number of cards the
        // cell shows (phase 10 keeps it equal to that as the poll adds and
        // removes cards, so a card moving in or out above it shifts the
        // server's list and the offset together). A card already on the
        // board is skipped, never shown twice.
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
                    const cell = container.closest('.kanban-cell');
                    const body = cell.querySelector('.kanban-cell-body');
                    const template = document.createElement('template');
                    template.innerHTML = data.html;

                    const cards = Array.from(template.content.querySelectorAll('.kanban-card'));
                    cards.forEach(function (card) {
                        if (cardEl(card.dataset.taskId)) return;
                        body.insertBefore(card, body.querySelector('.kanban-empty'));
                    });

                    // The cell's total as of this batch: what came before
                    // it, the batch, and what is left after it.
                    cell.dataset.total = String(offset + cards.length + data.remaining);
                    refreshCell(cell);
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
        // and move, update, add or remove cards (and, SOL-0025, lanes) in
        // place.
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
        // Updates held back for a busy card: id -> { change, seq } (change
        // null when the card was removed), seq being the poll it came from.
        // Applied when the card is free again.
        const pending = new Map();
        // Polls are numbered as they start. When your own change saves,
        // the card remembers the number of the latest poll started by
        // then: that poll, and any before it, may hold the card's state from
        // before the save, so they are ignored for it. The poll the save
        // triggers is numbered higher and brings the new state.
        let pollSeq = 0;
        const savedAfterPoll = new Map();
        // SOL-0025: set when a lane finishes loading - its response's server
        // time, for the next poll to read changes from (see loadLane()).
        let rewindTo = null;

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

        // "No tasks", "N more" and the Load more offset, from the cell's
        // total and the cards it shows. Unloaded lanes have no cards yet
        // and are left alone.
        function refreshCell(cell) {
            const shown = cell.querySelectorAll('.kanban-card').length;
            const total = Number(cell.dataset.total);
            const remaining = Math.max(0, total - shown);

            const empty = cell.querySelector('.kanban-empty');
            empty.hidden = shown > 0;
            empty.textContent = shown > 0 ? '' : 'No tasks';

            const more = cell.querySelector('.kanban-load-more');
            more.dataset.offset = String(shown);
            more.hidden = remaining === 0;
            more.querySelector('.kanban-more-count').textContent = remaining > 0 ? formatNumber(remaining) + ' more' : '';
        }

        // A lane's "N older completed · Show all" line.
        function setOlder(lane, count) {
            const older = lane.querySelector('.kanban-older');
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
            older.replaceChildren(document.createTextNode(formatNumber(count) + ' older completed · '), link);
        }

        // A new, closed lane from the page's template. The project id is
        // a number from the server; the name is set as text, never HTML.
        function newLane(info) {
            const template = document.createElement('template');
            template.innerHTML = laneTemplate.innerHTML.split('__PROJECT__').join(String(Number(info.id)));
            const lane = template.content.firstElementChild;
            lane.dataset.name = info.name;
            setLaneOpen(lane, false);

            return lane;
        }

        // A lane's header from the poll: name, Closed tag, "N tasks" and
        // its count per status; and, once loaded, each cell's total (read
        // by applyChanges() when it places cards, so set first).
        function updateLane(lane, info) {
            lane.dataset.name = info.name;
            const name = lane.querySelector('.kanban-lane-name');
            name.textContent = info.name;
            name.title = info.name;
            lane.querySelector('.kanban-lane-toggle').setAttribute('aria-label', (lane.dataset.open === '1' ? 'Close ' : 'Open ') + info.name);
            lane.querySelector('.kanban-lane-closed').hidden = ! info.closed;

            let total = 0;
            STATUSES.forEach(function (status) {
                const count = Number(info.totals[status] || 0);
                total += count;
                const countEl = lane.querySelector('.kanban-lane-count[data-status="' + status + '"]');
                countEl.textContent = formatNumber(count);
                countEl.classList.toggle('text-gray-400', count === 0);
                countEl.classList.toggle('font-medium', count > 0);
                countEl.classList.toggle('text-[#1F2937]', count > 0);
                if (lane.dataset.loaded === '1') cellOf(lane, status).dataset.total = String(count);
            });
            lane.querySelector('.kanban-lane-total').textContent = formatNumber(total) + ' ' + (total === 1 ? 'task' : 'tasks');
            if (lane.dataset.loaded === '1') setOlder(lane, info.older);
        }

        function laneHasBusyCard(lane) {
            return Array.from(lane.querySelectorAll('.kanban-card')).some(function (card) {
                return isBusy(card.dataset.taskId);
            });
        }

        // The lanes the server lists now, in its order: update each, add
        // the missing ones (closed) after the lane listed before them, and
        // remove the others - unless you're working on one of their cards,
        // then they go on a later flush.
        function applyLanes(lanes) {
            const listed = new Set(lanes.map(function (info) { return String(info.id); }));

            lanesEl.querySelectorAll('.kanban-lane').forEach(function (lane) {
                if (listed.has(lane.dataset.projectId)) {
                    delete lane.dataset.unlisted;
                } else {
                    lane.dataset.unlisted = '1';
                }
            });
            removeUnlistedLanes();

            let previous = null;
            lanes.forEach(function (info) {
                let lane = laneEl(info.id);
                if (! lane) {
                    lane = newLane(info);
                    lanesEl.insertBefore(lane, previous ? previous.nextElementSibling : lanesEl.firstElementChild);
                    updateLane(lane, info);
                    // SOL-0025 phase 2: a lane saved as open (one that
                    // went away and came back) reopens; others stay closed.
                    if (savedOpen.has(String(info.id))) openLane(info.id);
                } else {
                    updateLane(lane, info);
                }
                previous = lane;
            });

            // A renamed project's lane moves to its new place - only while
            // nothing is under the user (moving a lane would drop a drag or
            // a dropdown's focus); otherwise on a later poll.
            const ordered = lanes.map(function (info) { return laneEl(info.id); });
            const shown = Array.from(lanesEl.querySelectorAll('.kanban-lane')).filter(function (lane) {
                return listed.has(lane.dataset.projectId);
            });
            const outOfOrder = shown.some(function (lane, index) { return lane !== ordered[index]; });
            const focused = document.activeElement;
            const userBusy = draggingId !== null || busy.size > 0 || !! (focused && lanesEl.contains(focused) && focused.matches('select'));
            if (outOfOrder && ! userBusy) {
                ordered.forEach(function (lane) { lanesEl.appendChild(lane); });
            }

            document.getElementById('kanban-project-count').textContent = formatNumber(lanes.length);
        }

        // SOL-0025: "N older completed tasks in projects not shown · Show
        // all" - the company's older count less the lanes' own.
        function setHiddenOlder(companyOlder, lanes) {
            const line = document.getElementById('kanban-hidden-older');
            if (! line) return;
            const count = Math.max(0, Number(companyOlder) - lanes.reduce(function (sum, info) { return sum + Number(info.older); }, 0));

            line.hidden = count === 0;
            if (count === 0) {
                line.replaceChildren();
                return;
            }

            const link = document.createElement('a');
            link.href = line.dataset.showAllUrl;
            link.className = 'text-brand-700 hover:underline';
            link.textContent = 'Show all';
            line.replaceChildren(
                document.createTextNode(formatNumber(count) + ' older completed ' + (count === 1 ? 'task' : 'tasks') + ' in projects not shown · '),
                link,
            );
        }

        function removeUnlistedLanes() {
            lanesEl.querySelectorAll('.kanban-lane[data-unlisted="1"]').forEach(function (lane) {
                if (! laneHasBusyCard(lane)) lane.remove();
            });
            document.getElementById('kanban-no-lanes').hidden = lanesEl.querySelector('.kanban-lane') !== null;
            refreshLaneActions();
        }

        function toCardElement(change) {
            const template = document.createElement('template');
            template.innerHTML = change.html.trim();

            return template.content.firstElementChild;
        }

        // Apply changed cards and removed ids. A changed card always
        // replaces the one shown, wherever it is (SOL-0025: so a task
        // moved to another project leaves its old lane), and two saves in
        // the same second share an updated_at, so nothing is skipped as
        // "already seen". It goes in its project's lane - only if that
        // lane is loaded; an unloaded lane just has its counts - in the
        // cell of its status, before the first card with a larger sort
        // key. With no larger card it is appended only if the cell shows
        // everything: otherwise it belongs further down and will come with
        // Load more, and only "N more" changes.
        function applyChanges(cards, removed, seq) {
            const ready = [];
            // From a poll that started before your own save of this card.
            const predatesSave = function (id) {
                return savedAfterPoll.has(id) && seq <= savedAfterPoll.get(id);
            };

            removed.forEach(function (taskId) {
                const id = String(taskId);
                if (predatesSave(id)) return;
                if (isBusy(id)) {
                    pending.set(id, { change: null, seq: seq });
                    return;
                }
                pending.delete(id);
                const card = cardEl(id);
                if (card) card.remove();
            });

            cards.forEach(function (change) {
                const id = String(change.id);
                if (predatesSave(id)) return;
                if (isBusy(id)) {
                    pending.set(id, { change: change, seq: seq });
                    return;
                }
                pending.delete(id);
                const card = cardEl(id);
                if (card) card.remove();
                ready.push(change);
            });

            lanesEl.querySelectorAll('.kanban-lane[data-loaded="1"]').forEach(function (lane) {
                lane.querySelectorAll('.kanban-cell').forEach(function (cell) {
                    const body = cell.querySelector('.kanban-cell-body');
                    const incoming = ready
                        .filter(function (change) {
                            return String(change.project_id) === lane.dataset.projectId && change.status === cell.dataset.status;
                        })
                        .map(toCardElement)
                        .filter(Boolean)
                        .sort(function (a, b) { return a.dataset.sortKey < b.dataset.sortKey ? -1 : 1; });
                    const shown = body.querySelectorAll('.kanban-card').length;
                    const showsAll = Number(cell.dataset.total) <= shown + incoming.length;

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

                    refreshCell(cell);
                });
            });
        }

        // Updates held back for cards that are free now, then any lane
        // that was kept only for a busy card.
        function flushPending() {
            pending.forEach(function (entry, id) {
                if (isBusy(id)) return;
                pending.delete(id);
                if (entry.change) {
                    applyChanges([entry.change], [], entry.seq);
                } else {
                    applyChanges([], [id], entry.seq);
                }
            });
            removeUnlistedLanes();
        }

        function stop() {
            stopped = true;
            clearInterval(timer);
            timer = null;
            document.getElementById('kanban-live-note').hidden = false;
        }

        function pollOnce() {
            const seq = ++pollSeq;
            const url = new URL(changesUrl, window.location.origin);
            const rewound = rewindTo;
            url.searchParams.set('since', String(rewound === null ? cursor : Math.min(Number(cursor), rewound)));

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
                    // Stopped while this poll was in flight (e.g. a save
                    // found the session expired): apply nothing, so the
                    // card keeps its session-expired message.
                    if (! data || stopped) return;
                    if (data.stale) {
                        stop();
                        return;
                    }

                    // Advanced only after a successful poll, so a failed
                    // one loses nothing. A rewind this poll covered is
                    // done (unless another lane loaded meanwhile).
                    cursor = String(data.cursor);
                    if (rewindTo === rewound) rewindTo = null;
                    board.querySelectorAll('.kanban-column-header').forEach(function (header) {
                        if (header.dataset.status in data.totals) {
                            header.querySelector('.kanban-column-total').textContent = formatNumber(Number(data.totals[header.dataset.status]));
                        }
                    });
                    // Lanes first: the cells' totals must be current
                    // before applyChanges() decides where cards go.
                    applyLanes(data.lanes || []);
                    setHiddenOlder(data.older || 0, data.lanes || []);
                    applyChanges(data.cards, data.removed, seq);
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

        // SOL-0025 phase 2: the lanes this user left open, top first; their
        // cards load through the queue, 3 at a time. Not a save.
        JSON.parse(board.dataset.openLanes || '[]').forEach(openLane);
        refreshLaneActions();
    })();
</script>
@endsection
