<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        foreach ($user->visibleOrganizationIds() as $organizationId) {
            if ($user->isManagementInOrg($organizationId)) {
                return true;
            }
        }

        return $user->accessPermissions()->where('allowed', true)->exists();
    }

    public function view(User $user, Task $task): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        if ($user->isManagementInOrg($task->organization_id)) {
            return true;
        }

        return $user->hasDepartmentAccess($task->organization_id, $task->department_id);
    }

    public function create(User $user, int $organizationId): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        return $user->isManagementInOrg($organizationId);
    }

    public function update(User $user, Task $task): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        if ($user->isManagementInOrg($task->organization_id)) {
            return true;
        }

        return $task->assignee_id === $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        return $user->isManagementInOrg($task->organization_id);
    }
}
