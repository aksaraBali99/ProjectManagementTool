{{-- Live subtask list: every add/toggle/title-edit/delete/assignee/due-date
     change saves immediately via AJAX. Self-contained per inclusion (uses
     document.currentScript rather than a global id) so it can be included
     many times on one page — once per drilldown row on the task list, or
     once on the Edit Task page.

     Assignee/due date default to the parent task's current values as a
     one-time starting point at creation — not an ongoing sync, so editing
     the parent afterward never touches already-created subtasks. --}}
<div class="subtask-container" data-task-id="{{ $task->id }}" data-can-edit="{{ $canEdit ? '1' : '0' }}"
    data-task-start-date="{{ $task->start_date?->toDateString() }}" data-task-due-date="{{ $task->due_date?->toDateString() }}">
    <div class="subtask-rows space-y-2">
        @foreach ($task->subtasks as $subtask)
            <div data-subtask-id="{{ $subtask->id }}">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" class="subtask-desc-toggle text-[11px] text-gray-500 hover:text-gray-700" title="Description">+</button>
                    <input type="checkbox" class="subtask-toggle rounded border-gray-300 text-brand-600 focus:ring-brand-600" {{ $subtask->is_done ? 'checked' : '' }} {{ \App\Support\DeactivatedTask::allowsChanges($task) ? '' : 'disabled' }}>
                    <input type="text" value="{{ $subtask->title }}" {{ $canEdit ? '' : 'disabled' }}
                        class="subtask-title-input min-w-[140px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600 disabled:border-transparent disabled:bg-transparent disabled:px-0">
                    <select class="subtask-assignee-select w-28 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600 disabled:border-transparent disabled:bg-transparent" {{ $canEdit ? '' : 'disabled' }}>
                        <option value="">Unassigned</option>
                        @foreach ($staffOptions as $staff)
                            <option value="{{ $staff['id'] }}" {{ (int) $subtask->assignee_id === $staff['id'] ? 'selected' : '' }}>{{ $staff['name'] }}</option>
                        @endforeach
                        {{-- task #70 (unified eligibility): a subtask's CURRENT
                             assignee must still display correctly even if they
                             no longer qualify under the new rule (e.g. lost
                             department access after being assigned) — the new
                             rule governs future assignment, not existing data. --}}
                        @if ($subtask->assignee_id && ! collect($staffOptions)->contains('id', $subtask->assignee_id))
                            <option value="{{ $subtask->assignee_id }}" selected>{{ $subtask->assignee->name }}</option>
                        @endif
                    </select>
                    <input type="date" value="{{ $subtask->start_date?->toDateString() }}" {{ $canEdit ? '' : 'disabled' }} title="Start date"
                        class="subtask-start-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600 disabled:border-transparent disabled:bg-transparent">
                    <span class="shrink-0 text-[10px] text-gray-400">To</span>
                    <input type="date" value="{{ $subtask->due_date?->toDateString() }}" {{ $canEdit ? '' : 'disabled' }} title="Due date"
                        class="subtask-due-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600 disabled:border-transparent disabled:bg-transparent">
                    @if ($canEdit)
                        <button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Delete</button>
                    @endif
                    <span class="subtask-feedback text-[10px] text-gray-400"></span>
                </div>
                {{-- SOL-0022: rich text, the task Description's view/edit split
                     (tasks/_description-field.blade.php) in a compact form.
                     View: the description through <x-rich-text>, so links
                     work and nothing unsanitized is printed. data-raw-html
                     seeds the editor (RichText::toHtml(), never the stored
                     value as is). The pencil only for someone who can edit. --}}
                <div class="subtask-description-row mt-1 pl-5" style="display: none;">
                    <div class="subtask-description-view-row flex items-start justify-between gap-3 rounded-md border border-gray-300 bg-white px-3 py-2"
                        data-raw-html="{{ \App\Support\RichText::toHtml($subtask->description) }}">
                        <div class="subtask-description-view min-w-0 flex-1 text-[12px] text-[#1F2937]">
                            <x-rich-text :value="$subtask->description" empty="No description." />
                        </div>
                        @if ($canEdit)
                            <button type="button" class="edit-subtask-description-btn shrink-0 text-gray-500 hover:text-gray-700" title="Edit description" aria-label="Edit description">
                                <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.5 2.5a1.5 1.5 0 0 1 2 2L5 13l-3 1 1-3z"/><path d="M9.5 4.5l2 2"/></svg>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        @if ($task->subtasks->isEmpty())
            <p class="subtask-empty text-[11px] text-gray-500">No subtasks yet.</p>
        @endif
    </div>

    @if ($canEdit)
        <div class="mt-2 flex flex-wrap items-center gap-2">
            <input type="text" class="new-subtask-title min-w-[140px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" placeholder="New subtask title">
            <select class="new-subtask-assignee w-28 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                <option value="">Unassigned</option>
                @foreach ($staffOptions as $staff)
                    <option value="{{ $staff['id'] }}" {{ (int) $task->assignee_id === $staff['id'] ? 'selected' : '' }}>{{ $staff['name'] }}</option>
                @endforeach
            </select>
            <input type="date" class="new-subtask-start-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" title="Start date" value="{{ $task->start_date?->toDateString() }}">
            <span class="shrink-0 text-[10px] text-gray-400">To</span>
            <input type="date" class="new-subtask-due-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" title="Due date" value="{{ $task->due_date?->toDateString() }}">
            <button type="button" class="add-subtask-btn rounded-md border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                Add
            </button>
        </div>
        {{-- Why the last Add was refused; stays until the row is changed. --}}
        <p class="new-subtask-error mt-1 text-[10px] text-red-600" hidden></p>
        <p class="mt-1 text-[10px] text-gray-400">Assignee and due date default to this task's current values — change them per subtask as needed.</p>
    @endif
</div>
<script>
    (function () {
        const container = document.currentScript.previousElementSibling;
        const rowsEl = container.querySelector('.subtask-rows');
        const taskId = container.dataset.taskId;
        const canEdit = container.dataset.canEdit === '1';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function request(url, method, body) {
            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: body ? JSON.stringify(body) : undefined,
            });
        }

        // Surfaces the backend's actual validation/authorization message
        // (e.g. "Assignee must be assigned to this project.") instead of a
        // generic "failed" string, falling back to that generic string only
        // when the response carries no usable message of its own.
        function requestOrThrow(url, method, body, fallback) {
            return request(url, method, body).then(function (response) {
                if (response.ok) return response;
                return response.json().catch(function () { return null; }).then(function (data) {
                    const fieldErrors = data && data.errors ? Object.values(data.errors)[0] : null;
                    const message = (Array.isArray(fieldErrors) && fieldErrors[0]) || (data && data.message) || fallback;
                    const error = new Error(message);
                    // SOL-0022: lets a caller tell "the task's due date would
                    // have to move" (needs_task_due_extension) from a real error.
                    error.data = data || {};
                    throw error;
                });
            });
        }

        function showFeedback(row, message, isError) {
            const feedback = row.querySelector('.subtask-feedback');
            if (! feedback) return;
            feedback.textContent = message;
            feedback.classList.toggle('text-red-600', !! isError);
            feedback.classList.toggle('text-gray-400', ! isError);
            setTimeout(function () { feedback.textContent = ''; }, 2000);
        }

        // SOL-0022: date rules, mirrored from SubtaskController (the real
        // guard). Dates are YYYY-MM-DD strings, which sort correctly.
        const dueBeforeStartMessage = @json(\App\Support\DateRange::DUE_BEFORE_START);

        function datesInvalid(startValue, dueValue) {
            return !! (startValue && dueValue && dueValue < startValue);
        }

        // Greys out the invalid days in each date picker.
        function syncDateBounds(startInput, dueInput) {
            if (! startInput || ! dueInput) return;
            if (startInput.value) dueInput.min = startInput.value; else dueInput.removeAttribute('min');
            if (dueInput.value) startInput.max = dueInput.value; else startInput.removeAttribute('max');
        }

        function formatDate(value) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const parts = value.split('-');
            return Number(parts[2]) + ' ' + months[Number(parts[1]) - 1] + ' ' + parts[0];
        }

        // A subtask starting before its task: only a note, nothing is
        // blocked or moved.
        function updateDateNote(row) {
            const note = row.querySelector('.subtask-date-note');
            const startInput = row.querySelector('.subtask-start-date');
            if (! note || ! startInput) return;
            const taskStart = container.dataset.taskStartDate;
            const early = !! (taskStart && startInput.value && startInput.value < taskStart);
            note.textContent = early ? 'Starts before the task (' + formatDate(taskStart) + ')' : '';
            note.hidden = ! early;
        }

        // Tells the Edit page (tasks/edit.blade.php) the latest subtask due
        // date, which its own Due date can't be earlier than.
        function announceSubtaskDueDates() {
            let latest = '';
            rowsEl.querySelectorAll('.subtask-due-date').forEach(function (input) {
                if (input.value && input.value > latest) latest = input.value;
            });
            document.dispatchEvent(new CustomEvent('solava:subtask-due-dates', { detail: { taskId: taskId, latestDue: latest } }));
        }

        // After a confirmed save moved the task's due date out.
        function applyTaskDueDate(data) {
            if (! data || ! ('task_due_date' in data)) return;
            const dueDate = data.task_due_date || '';
            const previous = container.dataset.taskDueDate || '';
            if (dueDate === previous) return;
            container.dataset.taskDueDate = dueDate;
            // The Add row starts with the task's due date: keep it in step,
            // so its untouched defaults still count as the task's own dates,
            // unless the user already chose another one.
            if (newDueDateInput && newDueDateInput.value === previous) {
                newDueDateInput.value = dueDate;
                syncDateBounds(newStartDateInput, newDueDateInput);
            }
            document.dispatchEvent(new CustomEvent('solava:task-due-changed', { detail: { taskId: taskId, dueDate: dueDate } }));
        }

        // Sends a subtask save. If the subtask would be due after its task,
        // the server refuses it (needs_task_due_extension) and nothing is
        // saved; the user is asked, and on OK the same request goes again
        // with extend_task_due_date, which moves the task's due date out.
        // Cancel rejects with error.cancelled, so the caller can revert.
        function saveWithDueCheck(url, method, payload, fallback) {
            return requestOrThrow(url, method, payload, fallback).catch(function (error) {
                if (! error.data || ! error.data.needs_task_due_extension) throw error;
                if (! confirm(error.message)) {
                    error.cancelled = true;
                    throw error;
                }
                return requestOrThrow(url, method, Object.assign({}, payload, { extend_task_due_date: true }), fallback);
            }).then(function (response) {
                return response.json();
            }).then(function (data) {
                applyTaskDueDate(data);
                return data;
            });
        }

        // SOL-0022: subtask descriptions are rich text, edited with the same
        // editor as the task Description and comments. app.js (which
        // mounts it) is a deferred module, so wait for it, as
        // _description-field.blade.php and _comments.blade.php do.
        function withEditor(root) {
            return new Promise(function (resolve) {
                function go() { window.solavaRichText.mount(root).then(resolve); }
                if (window.solavaRichText) go();
                else document.addEventListener('DOMContentLoaded', go);
            });
        }

        // The read-only view. Only server-made HTML goes in here:
        // description_html from SubtaskController (RichText::toHtml()).
        // highlight() is given the holder, which CONTAINS the
        // [data-rich-text-content] element: it looks inside its scope, and
        // builds link chips, code colours and the lightbox hooks.
        function renderDescription(holder, html) {
            holder.innerHTML = '';
            if (html) {
                const content = document.createElement('div');
                content.className = 'rich-text';
                content.setAttribute('data-rich-text-content', '');
                content.innerHTML = html;
                holder.appendChild(content);
                if (window.solavaRichText) window.solavaRichText.highlight(holder);
            } else {
                const empty = document.createElement('p');
                empty.textContent = 'No description.';
                holder.appendChild(empty);
            }
        }

        // The pencil swaps the view for a compact editor: text formatting,
        // links and link chips only (no upload or mention attributes, so
        // those toolbar buttons hide themselves). It saves when focus
        // leaves it, or on Ctrl/Cmd+Enter, like the task Description;
        // Escape closes it without saving.
        function wireDescriptionEditor(row) {
            const viewRow = row.querySelector('.subtask-description-view-row');
            const holder = row.querySelector('.subtask-description-view');
            const editBtn = row.querySelector('.edit-subtask-description-btn');
            let editor = null;
            let editRoot = null;
            let initialHtml = '';
            let saving = false;
            let waitingForLinks = false;

            // A link chip shows "Resolving link… <token>" in the text while
            // the server fetches the page (buildLinkPreview() in
            // rich-text-editor.js), then swaps it for the chip, or for a
            // plain link after at most 15 seconds. Closing the link bar
            // briefly takes focus out of the editor, which counts as
            // clicking away; saving then would store the placeholder and
            // close the editor before the swap. So a save waits for every
            // link to resolve, then goes ahead if focus has really left or
            // Ctrl/Cmd+Enter was pressed; if the user is still typing, a
            // later click-away saves.
            const RESOLVING_LINK = 'Resolving link… ';
            // Ctrl/Cmd+Enter was pressed while a link was resolving: save
            // once it has, wherever focus is.
            let submitWhenResolved = false;

            // Has focus really left this editor? The emoji picker and the
            // image alt-text box are added to <body>, not inside the
            // editor, so they count as inside: the same list as the
            // editor's own isBlurSettled() (rich-text-editor.js).
            function focusIsOutside() {
                const active = document.activeElement;
                if (! active || active === document.body) return true;
                if (editRoot && editRoot.contains(active)) return false;
                return ! (active.closest && active.closest('.rte-emoji-picker, .rte-alt-edit-btn, .rte-alt-prompt'));
            }

            function currentHtml() {
                return editor.isEmpty() ? '' : editor.getHTML();
            }

            // The editor swaps every placeholder within 15 seconds; 20 is
            // only reached if the text itself contains the placeholder
            // words, and then it saves as it is rather than waiting forever.
            function saveOnceLinksResolve(submitted) {
                if (submitted) submitWhenResolved = true;
                if (waitingForLinks) return;
                waitingForLinks = true;
                const started = Date.now();
                const timer = setInterval(function () {
                    if (! editor) {
                        clearInterval(timer);
                        waitingForLinks = false;
                        submitWhenResolved = false;
                        return;
                    }
                    const timedOut = Date.now() - started >= 20000;
                    if (currentHtml().includes(RESOLVING_LINK) && ! timedOut) return;
                    clearInterval(timer);
                    waitingForLinks = false;
                    const submitted = submitWhenResolved;
                    submitWhenResolved = false;
                    if (submitted || focusIsOutside()) save(submitted, timedOut);
                }, 250);
            }

            function close() {
                if (editor) editor.destroy();
                editor = null;
                if (editRoot) editRoot.remove();
                editRoot = null;
                viewRow.style.display = '';
            }

            // submitted: from Ctrl/Cmd+Enter. ignoreResolving: the wait for
            // links timed out (see saveOnceLinksResolve()).
            function save(submitted, ignoreResolving) {
                if (! editor || saving) return;
                const html = currentHtml();
                if (html.includes(RESOLVING_LINK) && ! ignoreResolving) {
                    saveOnceLinksResolve(submitted);
                    return;
                }
                if (html === initialHtml) {
                    close();
                    return;
                }

                saving = true;
                requestOrThrow('/subtasks/' + row.dataset.subtaskId, 'PUT', { description: html === '' ? null : html }, 'Failed to save.')
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        viewRow.dataset.rawHtml = data.description_html || '';
                        renderDescription(holder, data.description_html || '');
                        showFeedback(row, 'Saved');
                        // Typed more while it was saving (focus stays in the
                        // editor after Ctrl/Cmd+Enter): keep the editor open
                        // with that text, so the next click-away saves it,
                        // rather than closing on the older saved version.
                        if (editor && currentHtml() !== html) {
                            initialHtml = html;
                            return;
                        }
                        close();
                    })
                    .catch(function (error) {
                        // The editor stays open with the text.
                        showFeedback(row, error.message, true);
                    })
                    .finally(function () { saving = false; });
            }

            editBtn.addEventListener('click', function () {
                if (editRoot) return;
                viewRow.style.display = 'none';

                editRoot = document.createElement('div');
                editRoot.className = 'subtask-description-editor rte-loading';
                editRoot.setAttribute('data-rich-text', '');
                editRoot.setAttribute('data-compact', '');
                editRoot.dataset.content = viewRow.dataset.rawHtml || '';
                editRoot.dataset.label = 'Subtask description';
                editRoot.dataset.placeholder = 'Add a description…';
                editRoot.dataset.linkPreviewTaskId = taskId;
                editRoot.dataset.linkPreviewContext = 'subtask';
                viewRow.insertAdjacentElement('afterend', editRoot);

                const root = editRoot;
                root.addEventListener('rte:submit', function () { save(true); });
                // Capture phase: TipTap's own Escape shortcut takes focus out
                // of the editor and marks the key handled, so a normal
                // listener never gets to cancel. Only an Escape typed in the
                // text itself cancels: the link bar's and the emoji panel's
                // inputs keep theirs (they aren't in .ProseMirror), and so
                // does an open @/: suggestion list (.rte-mentions, added to
                // <body>), which Escape closes first.
                root.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape' || ! event.target.closest('.ProseMirror')) return;
                    if (document.querySelector('.rte-mentions')) return;
                    event.preventDefault();
                    event.stopPropagation();
                    close();
                }, true);

                withEditor(root).then(function (created) {
                    if (editRoot !== root) {
                        created.destroy();
                        return;
                    }
                    editor = created;
                    initialHtml = created.isEmpty() ? '' : created.getHTML();
                    created.focus();
                    // onSettledBlur() also fires when the link bar closes
                    // (Apply, Cancel or Escape), before the editor takes
                    // focus back on the next frame. Look again a moment
                    // later and save only if focus really stayed outside.
                    created.onSettledBlur(function () {
                        setTimeout(function () {
                            if (editRoot === root && focusIsOutside()) save(false);
                        }, 150);
                    });
                });
            });
        }

        function clearEmptyState() {
            const empty = rowsEl.querySelector('.subtask-empty');
            if (empty) empty.remove();
        }

        function wireRow(row) {
            const toggle = row.querySelector('.subtask-toggle');
            const titleInput = row.querySelector('.subtask-title-input');
            const assigneeSelect = row.querySelector('.subtask-assignee-select');
            const startDateInput = row.querySelector('.subtask-start-date');
            const dueDateInput = row.querySelector('.subtask-due-date');
            const removeBtn = row.querySelector('.remove-subtask-row');
            const descToggle = row.querySelector('.subtask-desc-toggle');
            const descRow = row.querySelector('.subtask-description-row');
            const descEditBtn = row.querySelector('.edit-subtask-description-btn');

            // Collapsed by default regardless of content — expanding is
            // purely a local show/hide, scoped to this row only, same "+"/"−"
            // pattern as the Task list's own drilldown.
            if (descToggle && descRow) {
                descToggle.addEventListener('click', function () {
                    const isHidden = descRow.style.display === 'none';
                    descRow.style.display = isHidden ? '' : 'none';
                    descToggle.textContent = isHidden ? '−' : '+';
                });
            }

            if (canEdit && descEditBtn) wireDescriptionEditor(row);

            toggle.addEventListener('change', function () {
                const subtaskId = row.dataset.subtaskId;
                requestOrThrow('/subtasks/' + subtaskId + '/toggle', 'PATCH', undefined, 'Failed to save.')
                    .then(function () {
                        showFeedback(row, 'Saved');
                    })
                    .catch(function (error) {
                        toggle.checked = ! toggle.checked;
                        showFeedback(row, error.message, true);
                    });
            });

            if (canEdit && titleInput) {
                let originalTitle = titleInput.value;
                titleInput.addEventListener('blur', function () {
                    const newTitle = titleInput.value.trim();
                    if (newTitle === originalTitle || newTitle === '') return;

                    const subtaskId = row.dataset.subtaskId;
                    requestOrThrow('/subtasks/' + subtaskId, 'PUT', { title: newTitle }, 'Failed to save.')
                        .then(function () {
                            originalTitle = newTitle;
                            showFeedback(row, 'Saved');
                        })
                        .catch(function (error) {
                            titleInput.value = originalTitle;
                            showFeedback(row, error.message, true);
                        });
                });
            }

            if (canEdit && assigneeSelect) {
                let originalAssignee = assigneeSelect.value;
                assigneeSelect.addEventListener('change', function () {
                    const newAssignee = assigneeSelect.value;
                    const subtaskId = row.dataset.subtaskId;
                    requestOrThrow('/subtasks/' + subtaskId, 'PUT', { assignee_id: newAssignee === '' ? null : Number(newAssignee) }, 'Failed to save.')
                        .then(function () {
                            originalAssignee = newAssignee;
                            showFeedback(row, 'Saved');
                        })
                        .catch(function (error) {
                            assigneeSelect.value = originalAssignee;
                            showFeedback(row, error.message, true);
                        });
                });
            }

            // SOL-0022: the "starts before the task" note, created here so
            // rows built after Add get it too.
            const feedback = row.querySelector('.subtask-feedback');
            if (feedback && ! row.querySelector('.subtask-date-note')) {
                const note = document.createElement('span');
                note.className = 'subtask-date-note text-[10px] text-amber-600';
                note.hidden = true;
                feedback.before(note);
            }
            syncDateBounds(startDateInput, dueDateInput);
            updateDateNote(row);

            // SOL-0022: one date per request, as before. An invalid pair is
            // never sent: the input goes back to its saved value.
            function wireDateInput(input, field) {
                let originalValue = input.value;
                input.addEventListener('change', function () {
                    const newValue = input.value;
                    const startValue = field === 'start_date' ? newValue : startDateInput.value;
                    const dueValue = field === 'due_date' ? newValue : dueDateInput.value;
                    if (datesInvalid(startValue, dueValue)) {
                        input.value = originalValue;
                        syncDateBounds(startDateInput, dueDateInput);
                        showFeedback(row, dueBeforeStartMessage, true);
                        return;
                    }

                    const payload = {};
                    payload[field] = newValue === '' ? null : newValue;
                    saveWithDueCheck('/subtasks/' + row.dataset.subtaskId, 'PUT', payload, 'Failed to save.')
                        .then(function () {
                            originalValue = newValue;
                            syncDateBounds(startDateInput, dueDateInput);
                            updateDateNote(row);
                            if (field === 'due_date') announceSubtaskDueDates();
                            showFeedback(row, 'Saved');
                        })
                        .catch(function (error) {
                            input.value = originalValue;
                            syncDateBounds(startDateInput, dueDateInput);
                            if (! error.cancelled) showFeedback(row, error.message, true);
                        });
                });
            }

            if (canEdit && startDateInput) wireDateInput(startDateInput, 'start_date');
            if (canEdit && dueDateInput) wireDateInput(dueDateInput, 'due_date');

            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    if (! confirm('Delete this subtask? This cannot be undone.')) return;

                    const subtaskId = row.dataset.subtaskId;
                    requestOrThrow('/subtasks/' + subtaskId, 'DELETE', undefined, 'Failed to delete.')
                        .then(function () {
                            row.remove();
                            announceSubtaskDueDates();
                            if (! rowsEl.querySelector('[data-subtask-id]')) {
                                const empty = document.createElement('p');
                                empty.className = 'subtask-empty text-[11px] text-gray-500';
                                empty.textContent = 'No subtasks yet.';
                                rowsEl.appendChild(empty);
                            }
                        })
                        .catch(function (error) {
                            showFeedback(row, error.message, true);
                        });
                });
            }
        }

        rowsEl.querySelectorAll('[data-subtask-id]').forEach(wireRow);

        const staffOptions = @json($staffOptions);

        function buildAssigneeSelectHtml(selectedId) {
            let html = '<select class="subtask-assignee-select w-28 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600"><option value="">Unassigned</option>';
            staffOptions.forEach(function (staff) {
                html += '<option value="' + staff.id + '"' + (String(staff.id) === String(selectedId) ? ' selected' : '') + '>' + staff.name + '</option>';
            });
            return html + '</select>';
        }

        const addBtn = container.querySelector('.add-subtask-btn');
        const newTitleInput = container.querySelector('.new-subtask-title');
        const newAssigneeSelect = container.querySelector('.new-subtask-assignee');
        const newStartDateInput = container.querySelector('.new-subtask-start-date');
        const newDueDateInput = container.querySelector('.new-subtask-due-date');
        // Shown under the Add row, not in an alert (errors stay next to the
        // field they're about); cleared once the row changes.
        const addError = container.querySelector('.new-subtask-error');
        function showAddError(message) {
            if (! addError) return;
            addError.textContent = message;
            addError.hidden = ! message;
        }

        if (addBtn) {
            syncDateBounds(newStartDateInput, newDueDateInput);
            [newStartDateInput, newDueDateInput].forEach(function (input) {
                input.addEventListener('change', function () {
                    syncDateBounds(newStartDateInput, newDueDateInput);
                    showAddError('');
                });
            });

            addBtn.addEventListener('click', function () {
                const title = newTitleInput.value.trim();
                if (title === '') return;

                // SOL-0022: the task's own dates, which the Add row starts
                // with, are allowed even when they're an invalid pair (a task
                // started late), same as SubtaskController::store().
                const inheritedDates = newStartDateInput.value === (container.dataset.taskStartDate || '')
                    && newDueDateInput.value === (container.dataset.taskDueDate || '');
                if (! inheritedDates && datesInvalid(newStartDateInput.value, newDueDateInput.value)) {
                    showAddError(dueBeforeStartMessage);
                    return;
                }
                showAddError('');

                const payload = {
                    title: title,
                    assignee_id: newAssigneeSelect.value === '' ? null : Number(newAssigneeSelect.value),
                    start_date: newStartDateInput.value === '' ? null : newStartDateInput.value,
                    due_date: newDueDateInput.value === '' ? null : newDueDateInput.value,
                };

                saveWithDueCheck('/tasks/' + taskId + '/subtasks', 'POST', payload, 'Failed to add subtask.')
                    .then(function (data) {
                        clearEmptyState();
                        const row = document.createElement('div');
                        row.dataset.subtaskId = data.subtask.id;
                        row.innerHTML = '<div class="flex flex-wrap items-center gap-2">'
                            + '<button type="button" class="subtask-desc-toggle text-[11px] text-gray-500 hover:text-gray-700" title="Description">+</button>'
                            + '<input type="checkbox" class="subtask-toggle rounded border-gray-300 text-brand-600 focus:ring-1 focus:ring-brand-600">'
                            + '<input type="text" value="' + data.subtask.title.replace(/"/g, '&quot;') + '" class="subtask-title-input min-w-[140px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">'
                            + buildAssigneeSelectHtml(data.subtask.assignee_id)
                            + '<input type="date" value="' + (data.subtask.start_date || '') + '" title="Start date" class="subtask-start-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">'
                            + '<span class="shrink-0 text-[10px] text-gray-400">To</span>'
                            + '<input type="date" value="' + (data.subtask.due_date || '') + '" title="Due date" class="subtask-due-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">'
                            + '<button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Delete</button>'
                            + '<span class="subtask-feedback text-[10px] text-gray-400"></span>'
                            + '</div>'
                            // SOL-0022: the same view/edit markup as a
                            // server-rendered row; the view and its raw copy
                            // are filled below from description_html, never
                            // through this string.
                            + '<div class="subtask-description-row mt-1 pl-5" style="display: none;">'
                            + '<div class="subtask-description-view-row flex items-start justify-between gap-3 rounded-md border border-gray-300 bg-white px-3 py-2">'
                            + '<div class="subtask-description-view min-w-0 flex-1 text-[12px] text-[#1F2937]"></div>'
                            + '<button type="button" class="edit-subtask-description-btn shrink-0 text-gray-500 hover:text-gray-700" title="Edit description" aria-label="Edit description">'
                            + '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11.5 2.5a1.5 1.5 0 0 1 2 2L5 13l-3 1 1-3z"/><path d="M9.5 4.5l2 2"/></svg>'
                            + '</button>'
                            + '</div>'
                            + '</div>';
                        row.querySelector('.subtask-description-view-row').dataset.rawHtml = data.description_html || '';
                        renderDescription(row.querySelector('.subtask-description-view'), data.description_html || '');
                        rowsEl.appendChild(row);
                        wireRow(row);
                        announceSubtaskDueDates();
                        newTitleInput.value = '';
                        // Assignee/due date inputs stay at the parent task's
                        // defaults for the next add, per spec — not reset.
                    })
                    .catch(function (error) {
                        if (! error.cancelled) showAddError(error.message);
                    });
            });
        }
    })();
</script>
