<?php

namespace App\Http\Requests\Tasks\Concerns;

use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;

/**
 * Shared assignee check for StoreTaskRequest/UpdateTaskRequest and
 * SubtaskController, so the assignment surfaces can't drift out of sync.
 */
trait ValidatesTaskAssignment
{
    /**
     * A task/subtask assignee must both hold the Staff role in the
     * project's company and actually be assigned to that project (via
     * project_staff) — matching what the Assignee dropdown itself offers,
     * not just anyone staff company-wide.
     */
    protected function isAssignableStaffForProject(Project $project, mixed $userId): bool
    {
        $isStaffInCompany = OrgMember::where('organization_id', $project->organization_id)
            ->where('user_id', $userId)
            ->whereHas('role', fn ($query) => $query->where('slug', Role::STAFF))
            ->exists();

        if (! $isStaffInCompany) {
            return false;
        }

        return $project->staff()->where('users.id', $userId)->exists();
    }
}
