@extends('layouts.authenticated')

@section('title', 'Edit task — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('projects.index', $project->organization_id) }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Projects</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Edit task</h1>
        @if ($task->trashed())
            <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Inactive</span>
        @endif
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-[8px] bg-[#E1F5EE] p-3 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($canEdit)
        @if ($errors->any())
            <div class="mt-4 rounded-[8px] bg-red-50 p-3 text-[12px] text-red-700">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tasks.update', $task) }}" class="mt-6 space-y-4" id="edit-task-form">
            @csrf
            @method('PUT')

            <div>
                <label for="project_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Project</label>
                <select id="project_id" name="project_id" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    @foreach ($projects as $proj)
                        <option value="{{ $proj->id }}" {{ (int) old('project_id', $task->project_id) === $proj->id ? 'selected' : '' }}>
                            {{ $proj->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="department_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Department</label>
                <select id="department_id" name="department_id" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                </select>
            </div>

            <div>
                <label for="title" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $task->title) }}" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <div>
                <label for="description" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="priority" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</label>
                    @php $priorityValue = old('priority', $task->priority); @endphp
                    <select id="priority" name="priority" required
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                        <option value="high" {{ $priorityValue === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ $priorityValue === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ $priorityValue === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</label>
                    @php $statusValue = old('status', $task->status); @endphp
                    <select id="status" name="status" required
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                        <option value="pending" {{ $statusValue === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ $statusValue === 'in_progress' ? 'selected' : '' }}>In progress</option>
                        <option value="in_review" {{ $statusValue === 'in_review' ? 'selected' : '' }}>In review</option>
                        <option value="completed" {{ $statusValue === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="assignee_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Assignee</label>
                    <select id="assignee_id" name="assignee_id"
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    </select>
                </div>

                <div>
                    <label for="due_date" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Due date</label>
                    <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $task->due_date?->toDateString()) }}"
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                    Save changes
                </button>
                <a href="{{ route('projects.index', $project->organization_id) }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>

        <script>
            (function () {
                const projectOrganizations = @json($projectOrganizations);
                const departmentsByOrg = @json($departmentsByOrganization);
                const staffByOrg = @json($staffByOrganization);
                const oldDepartment = @json(old('department_id', $task->department_id));
                const oldAssignee = @json(old('assignee_id', $task->assignee_id));

                const projectSelect = document.getElementById('project_id');
                const departmentSelect = document.getElementById('department_id');
                const assigneeSelect = document.getElementById('assignee_id');

                function refreshDependents() {
                    const orgId = projectOrganizations[projectSelect.value];

                    departmentSelect.innerHTML = '';
                    (departmentsByOrg[orgId] || []).forEach(function (dept) {
                        const option = document.createElement('option');
                        option.value = dept.id;
                        option.textContent = dept.name;
                        if (String(dept.id) === String(oldDepartment)) option.selected = true;
                        departmentSelect.appendChild(option);
                    });

                    assigneeSelect.innerHTML = '<option value="">Unassigned</option>';
                    (staffByOrg[orgId] || []).forEach(function (member) {
                        const option = document.createElement('option');
                        option.value = member.id;
                        option.textContent = member.name;
                        if (String(member.id) === String(oldAssignee)) option.selected = true;
                        assigneeSelect.appendChild(option);
                    });
                }

                projectSelect.addEventListener('change', refreshDependents);
                refreshDependents();
            })();
        </script>

        @if ($canDeactivate)
            <form method="POST" action="{{ route('tasks.toggle-active', $task) }}" class="mt-3">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-[11px] text-gray-500 hover:underline">
                    {{ $task->trashed() ? 'Activate' : 'Deactivate' }} task
                </button>
            </form>
        @endif
    @else
        <div class="mt-6 space-y-3 rounded-[10px] border border-gray-200 p-4 text-[12px]">
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Title</span><p class="mt-0.5 font-medium text-[#1F2937]">{{ $task->title }}</p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Description</span><p class="mt-0.5 text-gray-700">{{ $task->description ?: '—' }}</p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Priority</span><p class="mt-0.5 text-gray-700">{{ ucfirst($task->priority) }}</p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Status</span><p class="mt-0.5 text-gray-700">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Due date</span><p class="mt-0.5 text-gray-700">{{ $task->due_date?->format('M j, Y') ?? '—' }}</p></div>
        </div>
        <p class="mt-3 text-[11px] text-gray-500">You can view this task and toggle its subtasks, but only its assignee or a manager can edit it.</p>
    @endif

    <div class="mt-6">
        <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Subtasks</span>

        <div id="subtask-rows" class="mt-2 space-y-2" data-task-id="{{ $task->id }}" data-can-edit="{{ $canEdit ? '1' : '0' }}">
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
        </div>

        @if ($canEdit)
            <div class="mt-2 flex items-center gap-2">
                <input type="text" id="new-subtask-title" placeholder="New subtask title"
                    class="flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <button type="button" id="add-subtask-btn" class="rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                    Add
                </button>
            </div>
        @endif
    </div>

    <script>
        (function () {
            const container = document.getElementById('subtask-rows');
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
                            })
                            .catch(function () {
                                showFeedback(row, 'Failed to delete', true);
                            });
                    });
                }
            }

            container.querySelectorAll('[data-subtask-id]').forEach(wireRow);

            const addBtn = document.getElementById('add-subtask-btn');
            const newTitleInput = document.getElementById('new-subtask-title');
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
                            const row = document.createElement('div');
                            row.className = 'flex items-center gap-2';
                            row.dataset.subtaskId = data.subtask.id;
                            row.innerHTML = '<input type="checkbox" class="subtask-toggle rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]">'
                                + '<input type="text" value="' + data.subtask.title.replace(/"/g, '&quot;') + '" class="subtask-title-input flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">'
                                + '<button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Delete</button>'
                                + '<span class="subtask-feedback text-[10px] text-gray-400"></span>';
                            container.appendChild(row);
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
</div>
@endsection
