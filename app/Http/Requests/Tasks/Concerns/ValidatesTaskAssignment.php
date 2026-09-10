<?php

namespace App\Http\Requests\Tasks\Concerns;

use App\Models\Project;
use App\Models\User;

/**
 * Shared assignee check for StoreTaskRequest/UpdateTaskRequest and
 * SubtaskController, so the assignment surfaces can't drift out of sync.
 */
trait ValidatesTaskAssignment
{
    /**
     * A task/subtask assignee must be attached to the project — via
     * project_staff (any role) or project_clients (the project's client) —
     * or hold a global role (super_admin/owner), matching what the
     * Assignee dropdown itself offers
     * (TaskManagementController::staffOptionsByProject()). Not restricted
     * to a "Staff" role: management and the project's client are
     * assignable too, as long as they're actually attached to the
     * project. Global-role users are exempt from needing an attachment at
     * all, since they're never added to project_staff/project_clients in
     * the first place — see User::scopeWithGlobalRole().
     */
    protected function isAssignableStaffForProject(Project $project, mixed $userId): bool
    {
        if ($project->staff()->where('users.id', $userId)->exists()) {
            return true;
        }

        if ($project->clients()->where('users.id', $userId)->exists()) {
            return true;
        }

        return User::withGlobalRole()->whereKey($userId)->exists();
    }
}
