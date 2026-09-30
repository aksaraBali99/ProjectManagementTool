<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Shared by TaskManagementController (Add/Edit Task's Assignee dropdown,
 * and the Task List drilldown's subtask-assignee select) and
 * KanbanController (the Kanban card's inline assignee select) so none of
 * these surfaces can drift out of sync on who's offered as an assignee.
 *
 * task #70 (unified eligibility): both builders below are thin wrappers
 * around Task::eligibleAssigneesFor() — the same shared rule the mention
 * autocomplete and ValidatesTaskAssignment::isAssignableStaffForProject()
 * (the actual server-side acceptance check) also delegate to. Eligibility
 * now depends on BOTH project AND department (staff need active
 * department access to the SPECIFIC department, not just project
 * membership), so the old single-project-id-keyed shape isn't enough —
 * everything here is keyed by project id, then department id.
 */
trait BuildsAssigneeOptions
{
    /**
     * For the Add/Edit Task page's Department+Assignee cascade: every
     * (project, department) combination the VIEWER could actually pick —
     * $departmentsByOrganization is the exact same array cascadingOptions()
     * already builds for the Department <select> itself (already filtered
     * to departments the CURRENT viewer has access to), reused here so
     * this never computes eligible assignees for a department the viewer
     * wouldn't even be offered in the first place.
     *
     * @param  Collection<int, Project>  $projects
     * @param  array<int, array<int, array{id: int, name: string}>>  $departmentsByOrganization  keyed by organization id
     * @return array<int, array<int, array<int, array{id: int, name: string}>>> keyed by project id, then department id
     */
    private function eligibleAssigneesByProjectAndDepartment(Collection $projects, array $departmentsByOrganization): array
    {
        $pairs = $projects->flatMap(fn (Project $project) => collect($departmentsByOrganization[$project->organization_id] ?? [])
            ->map(fn (array $department) => ['project' => $project, 'departmentId' => $department['id']]));

        return $this->eligibleAssigneesForPairs($pairs);
    }

    /**
     * For Kanban and the Task List drilldown, where each task already has
     * a fixed department — only the (project, department) pairs actually
     * present among $tasks, not the viewer's full department dropdown
     * (there is none on these pages).
     *
     * @param  Collection<int, Task>  $tasks
     * @return array<int, array<int, array<int, array{id: int, name: string}>>> keyed by project id, then department id
     */
    private function eligibleAssigneesByTask(Collection $tasks): array
    {
        $pairs = $tasks
            ->filter(fn (Task $task) => $task->project !== null && $task->department_id !== null)
            ->map(fn (Task $task) => ['project' => $task->project, 'departmentId' => $task->department_id]);

        return $this->eligibleAssigneesForPairs($pairs);
    }

    /**
     * @param  Collection<int, array{project: Project, departmentId: int}>  $pairs
     * @return array<int, array<int, array<int, array{id: int, name: string}>>>
     */
    private function eligibleAssigneesForPairs(Collection $pairs): array
    {
        $result = [];

        foreach ($pairs->unique(fn (array $pair) => $pair['project']->id.':'.$pair['departmentId']) as $pair) {
            $project = $pair['project'];
            $departmentId = $pair['departmentId'];

            $result[$project->id][$departmentId] = Task::eligibleAssigneesFor($project->organization_id, $project->id, $departmentId)
                ->map(fn (User $user) => ['id' => $user->id, 'name' => $user->name])
                ->values();
        }

        return $result;
    }
}
