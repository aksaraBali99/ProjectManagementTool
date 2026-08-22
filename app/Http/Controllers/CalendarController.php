<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Organization;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Frappe Gantt renders a flat list of bars, not swimlanes — there's no
     * native "group by project" concept in the library. Grouping is
     * approximated by sorting tasks by project (so same-project tasks sit
     * together in the list) and giving each project a distinct bar color
     * via custom_class, cycling this palette. The project name is also
     * prefixed onto each bar's label so grouping is legible even without
     * relying on color alone.
     */
    private const PROJECT_COLOR_CLASSES = ['gantt-project-0', 'gantt-project-1', 'gantt-project-2', 'gantt-project-3', 'gantt-project-4', 'gantt-project-5'];

    public function __invoke(?Organization $organization = null): View
    {
        $user = auth()->user();

        $organizations = Organization::whereIn('id', $user->boardOrganizationIds('view_calendar'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('calendar', [
                'organizations' => $organizations,
                'organization' => null,
                'ganttTasks' => [],
                'emptyMessage' => $user->boardAccessDeniedReason('view_calendar')->message(),
            ]);
        }

        if (! $organization || ! $organizations->contains('id', $organization->id)) {
            $organization = $organizations->first();
        }

        // Only tasks with a due_date can be positioned on a timeline —
        // undated tasks have nothing to plot them by, so they're excluded
        // here rather than shown at some arbitrary fallback date. Same rule
        // applies to subtasks below.
        $tasks = Task::visibleTo($user, $organization->id)
            ->whereNotNull('due_date')
            ->with(['project', 'subtasks'])
            ->get()
            ->sortBy(fn (Task $task) => $task->project->name)
            ->values();

        $projectColorIndex = [];
        $subtasksByTask = [];
        $ganttTasks = $tasks->map(function (Task $task) use (&$projectColorIndex, &$subtasksByTask) {
            $projectId = $task->project_id;
            if (! array_key_exists($projectId, $projectColorIndex)) {
                $projectColorIndex[$projectId] = count($projectColorIndex) % count(self::PROJECT_COLOR_CLASSES);
            }
            $colorClass = self::PROJECT_COLOR_CLASSES[$projectColorIndex[$projectId]];

            $plottableSubtasks = $task->subtasks->whereNotNull('due_date');
            $subtasksByTask[$task->id] = $plottableSubtasks
                ->map(fn ($subtask) => $this->ganttBar(
                    id: 'subtask-'.$subtask->id,
                    name: '↳ '.$subtask->title,
                    startDate: $subtask->start_date,
                    dueDate: $subtask->due_date,
                    progress: $subtask->is_done ? 100 : 0,
                    customClass: $colorClass,
                    editUrl: route('tasks.edit', $task),
                ))
                ->values()
                ->all();

            return array_merge(
                $this->ganttBar(
                    id: (string) $task->id,
                    name: $task->project->name.': '.$task->title,
                    startDate: $task->start_date,
                    dueDate: $task->due_date,
                    progress: match ($task->status) {
                        TaskStatus::Pending => 0,
                        TaskStatus::InProgress => 50,
                        TaskStatus::InReview => 75,
                        TaskStatus::Completed => 100,
                    },
                    customClass: $colorClass,
                    editUrl: route('tasks.edit', $task),
                ),
                ['subtaskCount' => $plottableSubtasks->count()],
            );
        })->values()->all();

        return view('calendar', [
            'organizations' => $organizations,
            'organization' => $organization,
            'ganttTasks' => $ganttTasks,
            'subtasksByTask' => $subtasksByTask,
        ]);
    }

    /**
     * @return array{id: string, name: string, start: string, end: string, progress: int, custom_class: string, editUrl: string}
     */
    private function ganttBar(string $id, string $name, ?Carbon $startDate, Carbon $dueDate, int $progress, string $customClass, string $editUrl): array
    {
        $due = $dueDate->toDateString();

        // start_date is null until work actually begins (or someone
        // backdates it manually) — not-yet-started renders as a single-day
        // sliver at the due date rather than a misleading multi-day bar.
        // Guarded against start_date falling after due_date (e.g. a due
        // date pulled earlier after work already started) since Frappe
        // Gantt silently drops any bar whose end is before its start.
        $start = $startDate?->toDateString() ?? $due;
        $start = $start <= $due ? $start : $due;

        return [
            'id' => $id,
            'name' => $name,
            'start' => $start,
            'end' => $due,
            'progress' => $progress,
            'custom_class' => $customClass,
            'editUrl' => $editUrl,
        ];
    }
}
