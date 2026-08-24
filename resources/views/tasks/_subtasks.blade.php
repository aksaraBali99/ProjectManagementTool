{{-- Live subtask list: every add/toggle/title-edit/delete/assignee/due-date
     change saves immediately via AJAX. Self-contained per inclusion (uses
     document.currentScript rather than a global id) so it can be included
     many times on one page — once per drilldown row on the task list, or
     once on the Edit Task page.

     Assignee/due date default to the parent task's current values as a
     one-time starting point at creation — not an ongoing sync, so editing
     the parent afterward never touches already-created subtasks. --}}
<div class="subtask-container" data-task-id="{{ $task->id }}" data-can-edit="{{ $canEdit ? '1' : '0' }}">
    <div class="subtask-rows space-y-2">
        @foreach ($task->subtasks as $subtask)
            <div class="flex flex-wrap items-center gap-2" data-subtask-id="{{ $subtask->id }}">
                <input type="checkbox" class="subtask-toggle rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]" {{ $subtask->is_done ? 'checked' : '' }}>
                <input type="text" value="{{ $subtask->title }}" {{ $canEdit ? '' : 'disabled' }}
                    class="subtask-title-input min-w-[140px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75] disabled:border-transparent disabled:bg-transparent disabled:px-0">
                <select class="subtask-assignee-select w-28 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75] disabled:border-transparent disabled:bg-transparent" {{ $canEdit ? '' : 'disabled' }}>
                    <option value="">Unassigned</option>
                    @foreach ($staffOptions as $staff)
                        <option value="{{ $staff['id'] }}" {{ (int) $subtask->assignee_id === $staff['id'] ? 'selected' : '' }}>{{ $staff['name'] }}</option>
                    @endforeach
                </select>
                <input type="date" value="{{ $subtask->start_date?->toDateString() }}" {{ $canEdit ? '' : 'disabled' }} title="Start date"
                    class="subtask-start-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75] disabled:border-transparent disabled:bg-transparent">
                <span class="shrink-0 text-[10px] text-gray-400">To</span>
                <input type="date" value="{{ $subtask->due_date?->toDateString() }}" {{ $canEdit ? '' : 'disabled' }} title="Due date"
                    class="subtask-due-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75] disabled:border-transparent disabled:bg-transparent">
                @if ($canEdit)
                    <button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Delete</button>
                @endif
                <span class="subtask-feedback text-[10px] text-gray-400"></span>
            </div>
        @endforeach
        @if ($task->subtasks->isEmpty())
            <p class="subtask-empty text-[11px] text-gray-500">No subtasks yet.</p>
        @endif
    </div>

    @if ($canEdit)
        <div class="mt-2 flex flex-wrap items-center gap-2">
            <input type="text" class="new-subtask-title min-w-[140px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]" placeholder="New subtask title">
            <select class="new-subtask-assignee w-28 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <option value="">Unassigned</option>
                @foreach ($staffOptions as $staff)
                    <option value="{{ $staff['id'] }}" {{ (int) $task->assignee_id === $staff['id'] ? 'selected' : '' }}>{{ $staff['name'] }}</option>
                @endforeach
            </select>
            <input type="date" class="new-subtask-start-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]" title="Start date" value="{{ $task->start_date?->toDateString() }}">
            <span class="shrink-0 text-[10px] text-gray-400">To</span>
            <input type="date" class="new-subtask-due-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]" title="Due date" value="{{ $task->due_date?->toDateString() }}">
            <button type="button" class="add-subtask-btn rounded-md border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                Add
            </button>
        </div>
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
                    throw new Error(message);
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

            if (canEdit && startDateInput) {
                let originalStartDate = startDateInput.value;
                startDateInput.addEventListener('change', function () {
                    const newStartDate = startDateInput.value;
                    const subtaskId = row.dataset.subtaskId;
                    requestOrThrow('/subtasks/' + subtaskId, 'PUT', { start_date: newStartDate === '' ? null : newStartDate }, 'Failed to save.')
                        .then(function () {
                            originalStartDate = newStartDate;
                            showFeedback(row, 'Saved');
                        })
                        .catch(function (error) {
                            startDateInput.value = originalStartDate;
                            showFeedback(row, error.message, true);
                        });
                });
            }

            if (canEdit && dueDateInput) {
                let originalDueDate = dueDateInput.value;
                dueDateInput.addEventListener('change', function () {
                    const newDueDate = dueDateInput.value;
                    const subtaskId = row.dataset.subtaskId;
                    requestOrThrow('/subtasks/' + subtaskId, 'PUT', { due_date: newDueDate === '' ? null : newDueDate }, 'Failed to save.')
                        .then(function () {
                            originalDueDate = newDueDate;
                            showFeedback(row, 'Saved');
                        })
                        .catch(function (error) {
                            dueDateInput.value = originalDueDate;
                            showFeedback(row, error.message, true);
                        });
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    const subtaskId = row.dataset.subtaskId;
                    requestOrThrow('/subtasks/' + subtaskId, 'DELETE', undefined, 'Failed to delete.')
                        .then(function () {
                            row.remove();
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
            let html = '<select class="subtask-assignee-select w-28 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]"><option value="">Unassigned</option>';
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
        if (addBtn) {
            addBtn.addEventListener('click', function () {
                const title = newTitleInput.value.trim();
                if (title === '') return;

                const payload = {
                    title: title,
                    assignee_id: newAssigneeSelect.value === '' ? null : Number(newAssigneeSelect.value),
                    start_date: newStartDateInput.value === '' ? null : newStartDateInput.value,
                    due_date: newDueDateInput.value === '' ? null : newDueDateInput.value,
                };

                requestOrThrow('/tasks/' + taskId + '/subtasks', 'POST', payload, 'Failed to add subtask.')
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        clearEmptyState();
                        const row = document.createElement('div');
                        row.className = 'flex flex-wrap items-center gap-2';
                        row.dataset.subtaskId = data.subtask.id;
                        row.innerHTML = '<input type="checkbox" class="subtask-toggle rounded border-gray-300 text-[#1D9E75] focus:ring-1 focus:ring-[#1D9E75]">'
                            + '<input type="text" value="' + data.subtask.title.replace(/"/g, '&quot;') + '" class="subtask-title-input min-w-[140px] flex-1 rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">'
                            + buildAssigneeSelectHtml(data.subtask.assignee_id)
                            + '<input type="date" value="' + (data.subtask.start_date || '') + '" title="Start date" class="subtask-start-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">'
                            + '<span class="shrink-0 text-[10px] text-gray-400">To</span>'
                            + '<input type="date" value="' + (data.subtask.due_date || '') + '" title="Due date" class="subtask-due-date w-32 shrink-0 rounded-md border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">'
                            + '<button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Delete</button>'
                            + '<span class="subtask-feedback text-[10px] text-gray-400"></span>';
                        rowsEl.appendChild(row);
                        wireRow(row);
                        newTitleInput.value = '';
                        // Assignee/due date inputs stay at the parent task's
                        // defaults for the next add, per spec — not reset.
                    })
                    .catch(function (error) {
                        alert(error.message);
                    });
            });
        }
    })();
</script>
