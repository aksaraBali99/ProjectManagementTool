@extends('layouts.authenticated')

@section('title', 'Edit task — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('tasks.index', $task->organization_id) }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Tasks</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Edit task</h1>
        @if ($task->trashed())
            <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Inactive</span>
        @endif
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-brand-50 p-3 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    @if ($canEdit)
        @if ($errors->any())
            <div class="mt-4 rounded-md bg-red-50 p-3 text-[12px] text-red-700">
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
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
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
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                </select>
            </div>

            <div>
                <label for="title" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $task->title) }}" required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            </div>

            <div>
                <label for="description" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">{{ old('description', $task->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="priority" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</label>
                    @php $priorityValue = old('priority', $task->priority->value); @endphp
                    <select id="priority" name="priority" required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                        @foreach (\App\Enums\Priority::cases() as $priorityCase)
                            <option value="{{ $priorityCase->value }}" {{ $priorityValue === $priorityCase->value ? 'selected' : '' }}>{{ $priorityCase->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</label>
                    @php $statusValue = old('status', $task->status->value); @endphp
                    <select id="status" name="status" required
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                        @foreach (\App\Enums\TaskStatus::cases() as $statusCase)
                            <option value="{{ $statusCase->value }}" {{ $statusValue === $statusCase->value ? 'selected' : '' }}>{{ $statusCase->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="assignee_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Assignee</label>
                    <select id="assignee_id" name="assignee_id"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                    </select>
                </div>

                <div>
                    <label for="due_date" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Due date</label>
                    <input id="due_date" name="due_date" type="date" value="{{ old('due_date', $task->due_date?->toDateString()) }}"
                        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                </div>
            </div>

            <div>
                <label for="start_date" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Start date</label>
                <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $task->start_date?->toDateString()) }}"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                <p class="mt-1 text-[10px] text-gray-500">Left empty until the task moves to Active, then set to today automatically — editable any time.</p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                    Save changes
                </button>
                <a href="{{ route('projects.index', $project->organization_id) }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>

        <script>
            (function () {
                const projectOrganizations = @json($projectOrganizations);
                const departmentsByOrg = @json($departmentsByOrganization);
                const staffByProject = @json($staffByProject);
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
                    (staffByProject[projectSelect.value] || []).forEach(function (member) {
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
        <div class="mt-6 space-y-3 rounded-lg border border-gray-200 p-4 text-[12px]">
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Title</span><p class="mt-0.5 font-medium text-[#1F2937]">{{ $task->title }}</p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Description</span><p class="mt-0.5 text-gray-700">{{ $task->description ?: '—' }}</p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Priority</span><p class="mt-1"><x-badge :background="$task->priority->badgeBackground()" :text="$task->priority->badgeText()">{{ $task->priority->label() }}</x-badge></p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Status</span><p class="mt-1"><x-badge :background="$task->status->badgeBackground()" :text="$task->status->badgeText()">{{ $task->status->label() }}</x-badge></p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Start date</span><p class="mt-0.5 text-gray-700">{{ $task->start_date?->format('M j, Y') ?? '—' }}</p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Due date</span><p class="mt-0.5 text-gray-700">{{ $task->due_date?->format('M j, Y') ?? '—' }}</p></div>
        </div>
        <p class="mt-3 text-[11px] text-gray-500">You can view this task and toggle its subtasks, but only its assignee or a manager can edit it.</p>
    @endif

    <div class="mt-6">
        <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Subtasks</span>
        <div class="mt-2">
            @include('tasks._subtasks', ['task' => $task, 'canEdit' => $canEdit, 'staffOptions' => $staffByProject[$task->project_id] ?? []])
        </div>
    </div>

    <div class="mt-6">
        <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Documents</span>
        <div class="mt-2">
            @include('tasks._documents', ['task' => $task, 'canEdit' => $canEdit, 'attachedDocuments' => $attachedDocuments, 'availableDocuments' => $availableDocuments])
        </div>
    </div>

    <div class="mt-6">
        <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Comments</span>
        <div class="mt-2">
            @include('tasks._comments', ['task' => $task, 'mentionableUsers' => collect($staffByProject[$task->project_id] ?? [])->reject(fn ($member) => $member['id'] === auth()->id())->values()])
        </div>
    </div>
</div>
@endsection
