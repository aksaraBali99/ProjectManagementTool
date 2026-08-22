<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Organization;
use App\Models\Task;
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
        // here rather than shown at some arbitrary fallback date.
        $tasks = Task::visibleTo($user, $organization->id)
            ->whereNotNull('due_date')
            ->with(['project'])
            ->get()
            ->sortBy(fn (Task $task) => $task->project->name)
            ->values();

        $projectColorIndex = [];
        $ganttTasks = $tasks->map(function (Task $task) use (&$projectColorIndex) {
            $projectId = $task->project_id;
            if (! array_key_exists($projectId, $projectColorIndex)) {
                $projectColorIndex[$projectId] = count($projectColorIndex) % count(self::PROJECT_COLOR_CLASSES);
            }

            $dueDate = $task->due_date->toDateString();

            // start_date is null until the task actually moves to In
            // Progress (or someone backdates it manually) — a task that
            // hasn't started yet renders as a single-day sliver at its due
            // date rather than a misleading multi-day bar. Guarded against
            // start_date falling after due_date (e.g. a due date pulled
            // earlier after the task already started) since Frappe Gantt
            // silently drops any task whose end is before its start rather
            // than rendering it.
            $startDate = $task->start_date?->toDateString() ?? $dueDate;
            $startDate = $startDate <= $dueDate ? $startDate : $dueDate;

            return [
                'id' => (string) $task->id,
                'name' => $task->project->name.': '.$task->title,
                'start' => $startDate,
                'end' => $dueDate,
                'progress' => match ($task->status) {
                    TaskStatus::Pending => 0,
                    TaskStatus::InProgress => 50,
                    TaskStatus::InReview => 75,
                    TaskStatus::Completed => 100,
                },
                'custom_class' => self::PROJECT_COLOR_CLASSES[$projectColorIndex[$projectId]],
                'editUrl' => route('tasks.edit', $task),
            ];
        })->values()->all();

        return view('calendar', [
            'organizations' => $organizations,
            'organization' => $organization,
            'ganttTasks' => $ganttTasks,
        ]);
    }
}
