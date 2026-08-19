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
                    @php $priorityValue = old('priority', 'medium'); @endphp
                    <select id="priority" name="priority" required
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                        <option value="high" {{ $priorityValue === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ $priorityValue === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ $priorityValue === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</label>
                    @php $statusValue = old('status', 'pending'); @endphp
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
                    <input id="due_date" name="due_date" type="date" value="{{ old('due_date') }}"
                        class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                </div>
            </div>

            <div>
                <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Subtasks</span>
                <p class="mt-1 text-[11px] text-gray-500">Added once you save this task.</p>

                <div id="subtask-rows" class="mt-2 space-y-2">
                    @forelse (old('subtasks', []) as $subtaskTitle)
                        <div class="flex items-center gap-2">
                            <input type="checkbox" disabled class="rounded border-gray-300 text-gray-300">
                            <input type="text" name="subtasks[]" value="{{ $subtaskTitle }}" placeholder="Subtask title"
                                class="subtask-title-input flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                            <button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Remove</button>
                        </div>
                    @empty
                    @endforelse
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

                const subtaskRows = document.getElementById('subtask-rows');
                const addSubtaskBtn = document.getElementById('add-subtask-btn');

                function addSubtaskRow() {
                    const row = document.createElement('div');
                    row.className = 'flex items-center gap-2';
                    row.innerHTML = '<input type="checkbox" disabled class="rounded border-gray-300 text-gray-300">'
                        + '<input type="text" name="subtasks[]" placeholder="Subtask title" class="subtask-title-input flex-1 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">'
                        + '<button type="button" class="remove-subtask-row text-[11px] text-gray-500 hover:underline">Remove</button>';
                    subtaskRows.appendChild(row);
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
