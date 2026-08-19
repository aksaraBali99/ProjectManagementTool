{{-- Live subtask list: every add/toggle/title-edit/delete saves immediately
     via AJAX. Self-contained per inclusion (uses document.currentScript
     rather than a global id) so it can be included many times on one page —
     once per drilldown row on the task list, or once on the Edit Task page. --}}
<div class="subtask-container" data-task-id="{{ $task->id }}" data-can-edit="{{ $canEdit ? '1' : '0' }}">
    <div class="subtask-rows space-y-2">
        @foreach ($task->subtasks as $subtask)
            <div class="flex items-center gap-2" data-subtask-id="{{ $subtask->id }}">
                <input type="checkbox" class="subtask-toggle rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]" {{ $subtask->is_done ? 'checked' : '' }}>
                <input type="text" value="{{ $subtask->title }}" {{ $canEdit ? '' : 'disabled' }}
                    class="subtask-title-input flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75] disabled:border-transparent disabled:bg-transparent disabled:px-0">
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
        <div class="mt-2 flex items-center gap-2">
            <input type="text" class="new-subtask-title flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]" placeholder="New subtask title">
            <button type="button" class="add-subtask-btn rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                Add
            </button>
        </div>
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
            const removeBtn = row.querySelector('.remove-subtask-row');

            toggle.addEventListener('change', function () {
                const subtaskId = row.dataset.subtaskId;
                request('/subtasks/' + subtaskId + '/toggle', 'PATCH')
                    .then(function (response) {
                        if (! response.ok) throw new Error();
                        showFeedback(row, 'Saved');
                    })
                    .catch(function () {
                        toggle.checked = ! toggle.checked;
                        showFeedback(row, 'Failed to save', true);
                    });
            });

            if (canEdit && titleInput) {
                let originalTitle = titleInput.value;
                titleInput.addEventListener('blur', function () {
                    const newTitle = titleInput.value.trim();
                    if (newTitle === originalTitle || newTitle === '') return;

                    const subtaskId = row.dataset.subtaskId;
                    request('/subtasks/' + subtaskId, 'PUT', { title: newTitle })
                        .then(function (response) {
                            if (! response.ok) throw new Error();
                            originalTitle = newTitle;
                            showFeedback(row, 'Saved');
                        })
                        .catch(function () {
                            titleInput.value = originalTitle;
                            showFeedback(row, 'Failed to save', true);
                        });
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    const subtaskId = row.dataset.subtaskId;
                    request('/subtasks/' + subtaskId, 'DELETE')
                        .then(function (response) {
                            if (! response.ok) throw new Error();
                            row.remove();
                            if (! rowsEl.querySelector('[data-subtask-id]')) {
                                const empty = document.createElement('p');
                                empty.className = 'subtask-empty text-[11px] text-gray-500';
                                empty.textContent = 'No subtasks yet.';
                                rowsEl.appendChild(empty);
                            }
                        })
                        .catch(function () {
                            showFeedback(row, 'Failed to delete', true);
                        });
                });
            }
        }

        rowsEl.querySelectorAll('[data-subtask-id]').forEach(wireRow);

        const addBtn = container.querySelector('.add-subtask-btn');
        const newTitleInput = container.querySelector('.new-subtask-title');
        if (addBtn) {
            addBtn.addEventListener('click', function () {
                const title = newTitleInput.value.trim();
                if (title === '') return;

                request('/tasks/' + taskId + '/subtasks', 'POST', { title: title })
                    .then(function (response) {
                        if (! response.ok) throw new Error();
                        return response.json();
                    })
                    .then(function (data) {
                        clearEmptyState();
                        const row = document.createElement('div');
                        row.className = 'flex items-center gap-2';
                        row.dataset.subtaskId = data.subtask.id;
                        row.innerHTML = '<input type="checkbox" class="subtask-toggle rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]">'
                            + '<input type="text" value="' + data.subtask.title.replace(/"/g, '&quot;') + '" class="subtask-title-input flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">'
                            + '<button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Delete</button>'
                            + '<span class="subtask-feedback text-[10px] text-gray-400"></span>';
                        rowsEl.appendChild(row);
                        wireRow(row);
                        newTitleInput.value = '';
                    })
                    .catch(function () {
                        alert('Failed to add subtask.');
                    });
            });
        }
    })();
</script>
