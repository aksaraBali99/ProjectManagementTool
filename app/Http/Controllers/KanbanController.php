<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\BuildsAssigneeOptions;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Organization;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KanbanController extends Controller
{
    use BuildsAssigneeOptions, ResolvesCurrentOrganization;

    public function __invoke(?Organization $organization = null): View
    {
        $user = auth()->user();

        $organizations = Organization::whereIn('id', $user->boardOrganizationIds('view_kanban'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('kanban', [
                'organizations' => $organizations,
                'organization' => null,
                'columns' => collect(),
                'emptyMessage' => $user->boardAccessDeniedReason('view_kanban')->message(),
                'canCreate' => false,
                'staffByProject' => [],
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $tasks = Task::visibleTo($user, $organization->id)
            ->with(['project', 'department', 'assignee'])
            ->get();

        // Priority::cases() declaration order (High, Medium, Low) IS the
        // natural ordering — index it once rather than sorting each column
        // by the enum's string value, which would sort alphabetically
        // (high, low, medium) instead of by severity.
        $priorityWeight = [];
        foreach (Priority::cases() as $index => $priority) {
            $priorityWeight[$priority->value] = $index;
        }

        $columns = collect(TaskStatus::cases())->map(fn (TaskStatus $status) => [
            'status' => $status,
            'tasks' => $tasks->where('status', $status)
                ->sortBy(fn (Task $task) => $priorityWeight[$task->priority->value])
                ->values()
                ->map(fn (Task $task) => [
                    'task' => $task,
                    'canEdit' => Gate::allows('updateStatus', $task),
                    // Reassignment is gated by the FULL 'update' policy
                    // (create_edit_tasks + department access, or
                    // management, or the task's own current assignee) —
                    // deliberately not the narrower 'updateStatus' capability
                    // 'canEdit' above uses, since handing a task to someone
                    // else is a bigger action than moving your own card.
                    'canReassign' => Gate::allows('update', $task),
                ]),
        ]);

        $projectsInList = $tasks->pluck('project')->filter()->unique('id')->values();

        return view('kanban', [
            'organizations' => $organizations,
            'organization' => $organization,
            'columns' => $columns,
            // Same gate the Tasks list page's own "+ Add task" button uses
            // (create_edit_tasks, narrowed to allowed departments for
            // Staff) - viewing the board (view_kanban) and creating a task
            // in it are separate permissions, so this can't be assumed
            // from having reached this far.
            'canCreate' => Gate::allows('create', [Task::class, $organization->id]),
            'staffByProject' => $this->staffOptionsByProject($projectsInList),
        ]);
    }
}
