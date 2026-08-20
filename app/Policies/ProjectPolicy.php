<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Unconditional true, unchanged — the actual project list is separately
     * org/role-scoped in the controller (visibleOrganizationIds()), and
     * every role (including client) already holds view_projects in the
     * seed, so there's no meaningful capability gate to add here without
     * an organization to check it against.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        if (! $user->hasPermission('view_projects', $project->organization_id)) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($project->organization_id) || $user->isStaffInOrg($project->organization_id)) {
            return true;
        }

        return $user->isClientOnProject($project->id);
    }

    public function create(User $user, int $organizationId): bool
    {
        if (! $user->hasPermission('create_edit_projects', $organizationId)) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organizationId);
    }

    public function update(User $user, Project $project): bool
    {
        if (! $user->hasPermission('create_edit_projects', $project->organization_id)) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($project->organization_id);
    }
}
