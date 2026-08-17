<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        if ($user->isManagementInOrg($project->organization_id)) {
            return true;
        }

        if ($user->isStaffInOrg($project->organization_id)) {
            return true;
        }

        return $user->isClientOnProject($project->id);
    }

    public function create(User $user, int $organizationId): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        return $user->isManagementInOrg($organizationId);
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        return $user->isManagementInOrg($project->organization_id);
    }
}
