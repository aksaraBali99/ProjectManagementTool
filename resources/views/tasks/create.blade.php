@extends('layouts.authenticated')

@section('title', 'Add task — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('projects.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Projects</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Add task</h1>

    @if ($projects->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">
            You need at least one project before you can add a task.
            <a href="{{ route('projects.create') }}" class="text-[#1D9E75] hover:underline">Create a project</a>.
        </p>
    @else
        @if ($errors->any())
            <div class="mt-4 rounded-[8px] bg-red-50 p-3 text-[12px] text-red-700">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tasks.store') }}" class="mt-6 space-y-4" id="create-task-form">
            @csrf

            <div>
                <label for="project_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Project</label>
                <select id="project_id" name="project_id" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    @foreach ($projects as $proj)
                        <option value="{{ $proj->id }}" {{ (int) old('project_id', $project->id) === $proj->id ? 'selected' : '' }}>
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
                <input id="title" name="title" type="text" value="{{ old('title') }}" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <div>
                <label for="description" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="priority" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</label>
                    @php $priorityValue = old('priority', \App\Enums\Priority::Medium->value); @endphp
                    <select id="priority" name="priority" required
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                        @foreach (\App\Enums\Priority::cases() as $priorityCase)
                            <option value="{{ $priorityCase->value }}" {{ $priorityValue === $priorityCase->value ? 'selected' : '' }}>{{ $priorityCase->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</label>
                    @php $statusValue = old('status', \App\Enums\TaskStatus::Pending->value); @endphp
                    <select id="status" name="status" required
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                        @foreach (\App\Enums\TaskStatus::cases() as $statusCase)
                            <option value="{{ $statusCase->value }}" {{ $statusValue === $statusCase->value ? 'selected' : '' }}>{{ $statusCase->label() }}</option>
                        @endforeach
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
                    <input id="due_date" name="due_date" type="date" value="{{ old('due_date') }}"
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                </div>
            </div>

            <div>
                <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Subtasks</span>
                <p class="mt-1 text-[11px] text-gray-500">Added once you save this task. Assignee and due date default to this task's — change either per subtask as needed.</p>

                <div id="subtask-rows" class="mt-2 space-y-2">
                    @foreach (old('subtasks', []) as $index => $staged)
                        <div class="flex items-center gap-2" data-subtask-row>
                            <input type="checkbox" disabled class="rounded border-gray-300 text-gray-300">
                            <input type="text" name="subtasks[{{ $index }}][title]" value="{{ $staged['title'] ?? '' }}" placeholder="Subtask title"
                                class="subtask-title-input flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                            <select name="subtasks[{{ $index }}][assignee_id]" class="subtask-assignee-select w-28 shrink-0 rounded-[8px] border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                                <option value="">Unassigned</option>
                            </select>
                            <input type="date" name="subtasks[{{ $index }}][due_date]" value="{{ $staged['due_date'] ?? '' }}"
                                class="subtask-due-date w-32 shrink-0 rounded-[8px] border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                            <button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Remove</button>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="add-subtask-btn" class="mt-2 text-[11px] font-medium text-[#1D9E75] hover:underline">
                    + Add subtask
                </button>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                    Create task
                </button>
                <a href="{{ route('projects.index') }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>

        <script>
            (function () {
                const projectOrganizations = @json($projectOrganizations);
                const departmentsByOrg = @json($departmentsByOrganization);
                const staffByOrg = @json($staffByOrganization);
                const oldDepartment = @json(old('department_id'));
                const oldAssignee = @json(old('assignee_id'));

                const projectSelect = document.getElementById('project_id');
                const departmentSelect = document.getElementById('department_id');
                const assigneeSelect = document.getElementById('assignee_id');
                const dueDateInput = document.getElementById('due_date');
                const subtaskRows = document.getElementById('subtask-rows');
                let subtaskIndex = subtaskRows.querySelectorAll('[data-subtask-row]').length;

                function populateAssigneeSelect(select, orgId, selectedId) {
                    select.innerHTML = '<option value="">Unassigned</option>';
                    (staffByOrg[orgId] || []).forEach(function (member) {
                        const option = document.createElement('option');
                        option.value = member.id;
                        option.textContent = member.name;
                        if (String(member.id) === String(selectedId)) option.selected = true;
                        select.appendChild(option);
                    });
                }

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

                    populateAssigneeSelect(assigneeSelect, orgId, oldAssignee);

                    // Staged subtask rows' assignee options depend on the same
                    // company as the parent task, so they need refreshing too —
                    // clearing the selection, since a previously chosen assignee
                    // may not belong to the newly selected project's company.
                    subtaskRows.querySelectorAll('.subtask-assignee-select').forEach(function (select) {
                        populateAssigneeSelect(select, orgId, null);
                    });
                }

                projectSelect.addEventListener('change', refreshDependents);
                refreshDependents();

                const addSubtaskBtn = document.getElementById('add-subtask-btn');

                function addSubtaskRow() {
                    const index = subtaskIndex++;
                    const orgId = projectOrganizations[projectSelect.value];

                    const row = document.createElement('div');
                    row.className = 'flex items-center gap-2';
                    row.dataset.subtaskRow = '';
                    row.innerHTML = '<input type="checkbox" disabled class="rounded border-gray-300 text-gray-300">'
                        + '<input type="text" name="subtasks[' + index + '][title]" placeholder="Subtask title" class="subtask-title-input flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">'
                        + '<select name="subtasks[' + index + '][assignee_id]" class="subtask-assignee-select w-28 shrink-0 rounded-[8px] border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]"></select>'
                        + '<input type="date" name="subtasks[' + index + '][due_date]" class="subtask-due-date w-32 shrink-0 rounded-[8px] border border-gray-300 px-1.5 py-2 text-[11px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">'
                        + '<button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Remove</button>';
                    subtaskRows.appendChild(row);

                    // Pre-fill from the parent task's current values — a
                    // starting point only, fully editable right away.
                    populateAssigneeSelect(row.querySelector('.subtask-assignee-select'), orgId, assigneeSelect.value);
                    row.querySelector('.subtask-due-date').value = dueDateInput.value;

                    row.querySelector('.subtask-title-input').focus();
                }

                addSubtaskBtn.addEventListener('click', addSubtaskRow);

                subtaskRows.addEventListener('click', function (event) {
                    const removeBtn = event.target.closest('.remove-subtask-row');
                    if (removeBtn) {
                        removeBtn.closest('div').remove();
                    }
                });
            })();
        </script>
    @endif
</div>
@endsection
