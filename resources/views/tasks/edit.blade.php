@extends('layouts.authenticated')

@section('title', 'Edit task — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="flex items-center justify-between">
        <a href="{{ $returnToUrl }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← {{ $returnToLabel }}</a>
        @if ($canEdit && $canDeactivate)
            <form method="POST" action="{{ route('tasks.toggle-active', $task) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-[13px] text-gray-500 hover:underline">
                    {{ $task->trashed() ? 'Activate' : 'Deactivate' }} task
                </button>
            </form>
        @endif
    </div>

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
                <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Description</span>
                {{-- View/edit split, autosave on blur instead of Save/Cancel
                     (task #4, description autosave) — see
                     tasks/_description-field.blade.php. Always reached with
                     edit permission here (this whole block is inside
                     @if ($canEdit), the identical TaskPolicy::update check
                     Description's own image/audio upload already uses), so
                     the Edit control is unconditional in this partial. --}}
                @include('tasks._description-field', ['task' => $task, 'value' => old('description', $task->description)])
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
                <a href="{{ $returnToUrl }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>

        <script>
            (function () {
                const projectOrganizations = @json($projectOrganizations);
                const departmentsByOrg = @json($departmentsByOrganization);
                const eligibleAssignees = @json($eligibleAssignees);
                const oldDepartment = @json(old('department_id', $task->department_id));
                const oldAssignee = @json(old('assignee_id', $task->assignee_id));
                // task #70 (unified eligibility): the task's CURRENT assignee
                // must still display correctly even if they no longer
                // qualify under the new rule (e.g. they lost department
                // access after being assigned) — the new rule governs future
                // assignment, not existing data. Only used as a fallback
                // when oldAssignee is genuinely this task's own saved
                // assignee (not a different, rejected redisplay value).
                const currentAssigneeId = @json($task->assignee_id);
                const currentAssigneeName = @json($task->assignee?->name);

                const projectSelect = document.getElementById('project_id');
                const departmentSelect = document.getElementById('department_id');
                const assigneeSelect = document.getElementById('assignee_id');

                function populateAssigneeSelect(select, projectId, departmentId, selectedId, fallbackName) {
                    select.innerHTML = '<option value="">Unassigned</option>';
                    let found = false;
                    ((eligibleAssignees[projectId] || {})[departmentId] || []).forEach(function (member) {
                        const option = document.createElement('option');
                        option.value = member.id;
                        option.textContent = member.name;
                        if (String(member.id) === String(selectedId)) {
                            option.selected = true;
                            found = true;
                        }
                        select.appendChild(option);
                    });

                    if (selectedId && ! found && fallbackName) {
                        const option = document.createElement('option');
                        option.value = selectedId;
                        option.textContent = fallbackName;
                        option.selected = true;
                        select.appendChild(option);
                    }
                }

                // task #70 (unified eligibility): who's assignable now
                // depends on BOTH the selected project AND department —
                // re-run whenever either changes, not just the project.
                function refreshAssigneeOptions() {
                    const fallbackName = String(oldAssignee) === String(currentAssigneeId) ? currentAssigneeName : null;
                    populateAssigneeSelect(assigneeSelect, projectSelect.value, departmentSelect.value, oldAssignee, fallbackName);
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

                    refreshAssigneeOptions();
                }

                projectSelect.addEventListener('change', refreshDependents);
                departmentSelect.addEventListener('change', refreshAssigneeOptions);
                refreshDependents();
            })();
        </script>
    @else
        <div class="mt-6 space-y-3 rounded-lg border border-gray-200 p-4 text-[12px]">
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Title</span><p class="mt-0.5 font-medium text-[#1F2937]">{{ $task->title }}</p></div>
            <div><span class="text-[10px] uppercase tracking-[0.05em] text-gray-500">Description</span><x-rich-text :value="$task->description" empty="—" class="mt-0.5 text-gray-700" /></div>
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
            @include('tasks._subtasks', ['task' => $task, 'canEdit' => $canEdit, 'staffOptions' => $eligibleAssignees[$task->project_id][$task->department_id] ?? []])
        </div>
    </div>

    <div class="mt-6">
        <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Documents</span>
        <div class="mt-2">
            @include('tasks._documents', ['task' => $task, 'canEdit' => $canEdit, 'canManageDocuments' => $canManageDocuments, 'canUnlinkDocuments' => $canUnlinkDocuments, 'canAttachDocuments' => $canAttachDocuments, 'projectHasClient' => $projectHasClient, 'attachedDocuments' => $attachedDocuments, 'linkedFolders' => $linkedFolders])
        </div>
    </div>

    <div class="mt-6">
        <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Comments</span>
        <div class="mt-2">
            @include('tasks._comments', ['task' => $task, 'mentionableUsers' => $task->viewableUsers()->reject(fn ($user) => $user->id === auth()->id())->map(fn ($user) => ['id' => $user->id, 'name' => $user->name])->values()])
        </div>
    </div>
</div>

@include('users._unsaved-changes-guard', ['formId' => 'edit-task-form'])
@endsection
