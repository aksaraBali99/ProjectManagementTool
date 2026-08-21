@extends('layouts.authenticated')

@section('title', 'Kanban — Solava')

@section('content')
<div>
    <h1 class="text-[14px] font-medium text-[#1F2937]">Kanban</h1>

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">{{ $emptyMessage ?? "You don't have access to any companies yet." }}</p>
    @else
        <div class="mt-4 flex gap-1 border-b border-gray-200">
            @foreach ($organizations as $tab)
                @php $isActiveTab = $organization && $organization->id === $tab->id @endphp
                <a href="{{ route('kanban', $tab) }}"
                   style="{{ $isActiveTab ? 'border-color: '.$tab->accent_color : '' }}"
                   class="border-b-2 px-4 py-2 text-[12px] {{ $isActiveTab ? 'font-medium text-[#1F2937]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    {{ $tab->name }}
                </a>
            @endforeach
        </div>

        <div id="kanban-board" class="mt-4 grid grid-cols-1 gap-4" style="grid-template-columns: repeat({{ $columns->count() }}, minmax(0, 1fr));">
            @foreach ($columns as $column)
                <div class="kanban-column rounded-[10px] border border-gray-200 bg-white" data-status="{{ $column['status']->value }}">
                    <div class="flex items-center justify-between border-b border-gray-200 px-3 py-2">
                        <span class="text-[11px] font-semibold uppercase tracking-[0.05em] text-gray-500">{{ $column['status']->label() }}</span>
                        <span class="text-[10px] text-gray-400">{{ $column['tasks']->count() }}</span>
                    </div>
                    <div class="kanban-column-body min-h-[80px] space-y-2 p-2">
                        @forelse ($column['tasks'] as $item)
                            @php [$task, $canEdit] = [$item['task'], $item['canEdit']] @endphp
                            <div class="kanban-card rounded-[8px] border border-gray-200 bg-white p-2 {{ $canEdit ? 'cursor-move' : '' }}"
                                 draggable="{{ $canEdit ? 'true' : 'false' }}" data-task-id="{{ $task->id }}">
                                <a href="{{ route('tasks.edit', $task) }}" class="text-[12px] font-medium text-[#1F2937] hover:underline">{{ $task->title }}</a>
                                <p class="mt-1 text-[10px] text-gray-500">
                                    {{ $task->project->name }}
                                    @if ($task->assignee) &middot; {{ $task->assignee->name }} @endif
                                    @if ($task->due_date) &middot; {{ $task->due_date->format('M j') }} @endif
                                </p>
                                <div class="mt-1.5 flex items-center justify-between gap-2">
                                    @if ($task->priority === \App\Enums\Priority::High)
                                        <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">{{ $task->priority->label() }}</span>
                                    @elseif ($task->priority === \App\Enums\Priority::Medium)
                                        <span class="rounded-[5px] bg-[#FDF1D9] px-2 py-0.5 text-[10px] font-medium text-[#8A5A00]">{{ $task->priority->label() }}</span>
                                    @else
                                        <span class="rounded-[5px] bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">{{ $task->priority->label() }}</span>
                                    @endif

                                    <select class="kanban-status-select rounded-[6px] border border-gray-300 px-1.5 py-0.5 text-[10px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]"
                                            data-task-id="{{ $task->id }}" {{ $canEdit ? '' : 'disabled' }}>
                                        @foreach (\App\Enums\TaskStatus::cases() as $statusOption)
                                            <option value="{{ $statusOption->value }}" {{ $task->status === $statusOption ? 'selected' : '' }}>{{ $statusOption->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @empty
                            <p class="py-4 text-center text-[11px] text-gray-400">No tasks</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    (function () {
        const board = document.getElementById('kanban-board');
        if (! board) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function changeStatus(taskId, status) {
            fetch('/tasks/' + taskId + '/status', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ status: status }),
            })
                .then(function (response) {
                    if (response.ok) {
                        window.location.reload();
                        return;
                    }
                    // Surfaces the backend's actual message (e.g. an
                    // authorization failure) instead of a generic string,
                    // falling back to that generic string only when the
                    // response carries no message of its own.
                    return response.json().catch(function () { return null; }).then(function (data) {
                        throw new Error((data && data.message) || 'Failed to change status.');
                    });
                })
                .catch(function (error) {
                    alert(error.message);
                    window.location.reload();
                });
        }

        board.querySelectorAll('.kanban-card[draggable="true"]').forEach(function (card) {
            card.addEventListener('dragstart', function (event) {
                event.dataTransfer.setData('text/plain', card.dataset.taskId);
            });
        });

        board.querySelectorAll('.kanban-column').forEach(function (column) {
            column.addEventListener('dragover', function (event) {
                event.preventDefault();
            });
            column.addEventListener('drop', function (event) {
                event.preventDefault();
                const taskId = event.dataTransfer.getData('text/plain');
                if (! taskId) return;
                changeStatus(taskId, column.dataset.status);
            });
        });

        board.querySelectorAll('.kanban-status-select').forEach(function (select) {
            select.addEventListener('change', function () {
                changeStatus(select.dataset.taskId, select.value);
            });
        });
    })();
</script>
@endsection
