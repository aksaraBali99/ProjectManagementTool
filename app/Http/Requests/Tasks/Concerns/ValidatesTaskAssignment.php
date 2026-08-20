<?php

namespace App\Http\Requests\Tasks\Concerns;

use App\Models\Project;

/**
 * Shared assignee check for StoreTaskRequest/UpdateTaskRequest and
 * SubtaskController, so the assignment surfaces can't drift out of sync.
 */
trait ValidatesTaskAssignment
{
    /**
     * A task/subtask assignee must be attached to the project — via
     * project_staff (any role) or project_clients (the project's client) —
     * matching what the Assignee dropdown itself offers. Not restricted to
     * a "Staff" role: management and the project's client are assignable
     * too, as long as they're actually attached to the project.
     */
    protected function isAssignableStaffForProject(Project $project, mixed $userId): bool
    {
        if ($project->staff()->where('users.id', $userId)->exists()) {
            return true;
        }

        return $project->clients()->where('users.id', $userId)->exists();
    }
}
