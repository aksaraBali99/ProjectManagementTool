<?php

namespace App\Http\Requests\Tasks\Concerns;

use App\Models\Project;
use App\Models\Task;

/**
 * Shared assignee check for StoreTaskRequest/UpdateTaskRequest and
 * SubtaskController, so the assignment surfaces can't drift out of sync.
 */
trait ValidatesTaskAssignment
{
    /**
     * task #70 (unified eligibility): a thin wrapper around
     * Task::eligibleAssigneesFor() — the same shared rule the Assignee
     * dropdown's own options list (BuildsAssigneeOptions) and the mention
     * autocomplete (Task::viewableUsers()) both delegate to, so none of
     * the three can drift out of sync with each other. $departmentId is
     * the department this task/subtask actually belongs (or is about to
     * belong) to — for a subtask, that's always its PARENT task's own
     * department_id, since subtasks have no department of their own.
     */
    protected function isAssignableStaffForProject(Project $project, int $departmentId, mixed $userId): bool
    {
        return Task::eligibleAssigneesFor($project->organization_id, $project->id, $departmentId)->contains('id', $userId);
    }
}
