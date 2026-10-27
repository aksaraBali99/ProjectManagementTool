{{--
    task #72 phase 5: one Kanban card, shared by the board and the Load
    more endpoint (KanbanController::column()) so both render identical
    cards. Moved here unchanged from kanban.blade.php, except that the
    title link's return_to is the board's own URL ($returnTo) passed in by
    the caller - inside Load more, url()->full() would be the JSON
    endpoint.

    Expects: $task, $canEdit, $canReassign, $eligibleAssignees, $returnTo.
--}}
@php
    // task #71: shown on the closed <select> itself in addition to the
    // avatar's own existing tooltip (avatar.blade.php always sets
    // title="{{ $user->name }}") — belt and braces once the option text
    // can be truncated below.
    $assigneeName = $task->assignee->name ?? 'Unassigned';
@endphp
<div class="kanban-card rounded-md border border-gray-200 p-2 {{ $canEdit ? 'cursor-move' : '' }}"
     style="background-color: {{ $task->priority->badgeBackground() }}"
     draggable="{{ $canEdit ? 'true' : 'false' }}" data-task-id="{{ $task->id }}">
    {{-- task #71: the avatar moved up into this title row (was a
         card-level sibling spanning the card's full height) — this row
         and the priority/assignee/status row below it are now both
         direct children of the card, with the same horizontal padding,
         so the avatar's right edge and the status select's right edge
         (the last item in that row — see below) land at the exact same
         x-position without any special-case alignment CSS. --}}
    <div class="flex items-start justify-between gap-2">
        <div class="min-w-0 flex-1">
            <a href="{{ route('tasks.edit', ['task' => $task, 'return_to' => $returnTo, 'return_label' => 'Kanban']) }}" class="text-[12px] font-medium text-[#1F2937] hover:underline">{{ $task->title }}</a>
            {{-- task #71: the task's own permanent database id, "#<id>" —
                 unrelated to the Import feature's file-scoped "Task Ref"
                 numbering. Folded into the existing muted metadata line
                 rather than a whole new line, so it doesn't compete with
                 the title. --}}
            <p class="mt-1 text-[10px] text-gray-500">
                #{{ $task->id }} &middot; {{ $task->project->name }}
                @if ($task->due_date) &middot; {{ $task->due_date->format('M j') }} @endif
            </p>
        </div>
        <x-avatar :user="$task->assignee" size="32px" class="shrink-0" />
    </div>
    {{-- task #71: Assignee moved up onto the SAME row as Status (was
         its own row below) — Assignee comes first so Status, being the
         last element, is what actually touches the row's right edge
         (see the avatar note above). The assignee select's width is now
         fixed (was sizing to fit whichever name happened to be selected)
         with `truncate` for anything longer — the avatar's own tooltip
         and this select's own title="" attribute both still show the
         full name on hover, the same reliance on a tooltip Calendar's
         own task-label truncation (Phase 8) already established. --}}
    <div class="mt-1.5 flex items-center justify-between gap-2">
        <x-badge :background="$task->priority->badgeBackground()" :text="$task->priority->badgeText()">{{ $task->priority->label() }}</x-badge>

        <div class="flex items-center gap-1.5">
            @php $kanbanAssigneeOptions = $eligibleAssignees[$task->project_id][$task->department_id] ?? []; @endphp
            <select class="kanban-assignee-select w-20 truncate rounded-md border border-gray-300 px-1.5 py-0.5 text-[10px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600"
                    data-task-id="{{ $task->id }}" title="{{ $assigneeName }}" {{ $canReassign ? '' : 'disabled' }}>
                <option value="">Unassigned</option>
                @foreach ($kanbanAssigneeOptions as $option)
                    <option value="{{ $option['id'] }}" {{ (int) $task->assignee_id === $option['id'] ? 'selected' : '' }}>{{ $option['name'] }}</option>
                @endforeach
                {{-- task #70 (unified eligibility): the CURRENT assignee
                     must still display correctly even if they no longer
                     qualify under the new rule — the new rule governs
                     future assignment, not existing data. --}}
                @if ($task->assignee_id && ! collect($kanbanAssigneeOptions)->contains('id', $task->assignee_id))
                    <option value="{{ $task->assignee_id }}" selected>{{ $assigneeName }}</option>
                @endif
            </select>

            <select class="kanban-status-select rounded-md border border-gray-300 px-1.5 py-0.5 text-[10px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600"
                    data-task-id="{{ $task->id }}" {{ $canEdit ? '' : 'disabled' }}>
                @foreach (\App\Enums\TaskStatus::cases() as $statusOption)
                    <option value="{{ $statusOption->value }}" {{ $task->status === $statusOption ? 'selected' : '' }}>{{ $statusOption->label() }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
