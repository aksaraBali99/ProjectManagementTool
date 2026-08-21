<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Organization;
use App\Models\Task;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KanbanController extends Controller
{
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
            ]);
        }

        if (! $organization || ! $organizations->contains('id', $organization->id)) {
            $organization = $organizations->first();
        }

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
                    'canEdit' => Gate::allows('update', $task),
                ]),
        ]);

        return view('kanban', [
            'organizations' => $organizations,
            'organization' => $organization,
            'columns' => $columns,
        ]);
    }
}
