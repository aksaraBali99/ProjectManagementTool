<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return $user->hasPermission('view_tasks');
        }

        foreach ($user->visibleOrganizationIds() as $organizationId) {
            if (! $user->hasPermission('view_tasks', $organizationId)) {
                continue;
            }

            if ($user->isManagementInOrg($organizationId)) {
                return true;
            }

            if ($user->accessPermissions()->where('organization_id', $organizationId)->where('allowed', true)->exists()) {
                return true;
            }

            if ($user->projectsAsClient()->where('organization_id', $organizationId)->exists()) {
                return true;
            }

            if (Task::where('organization_id', $organizationId)->where('assignee_id', $user->id)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function view(User $user, Task $task): bool
    {
        if (! $user->hasPermission('view_tasks', $task->organization_id)) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($task->organization_id)) {
            return true;
        }

        if ($user->hasDepartmentAccess($task->organization_id, $task->department_id)) {
            return true;
        }

        if ($task->assignee_id === $user->id) {
            return true;
        }

        return $user->isClientOnProject($task->project_id);
    }

    public function create(User $user, int $organizationId): bool
    {
        if (! $user->hasPermission('create_edit_tasks', $organizationId)) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organizationId);
    }

    /**
     * Two independent ways in: management-tier (gated by create_edit_tasks,
     * since that's the role-capability this represents), or being the
     * task's assignee (an identity/ownership check, not a role capability
     * — deliberately NOT gated by create_edit_tasks, since staff never hold
     * that permission but must still be able to edit tasks assigned to them).
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasPermission('create_edit_tasks', $task->organization_id)
            && ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($task->organization_id))) {
            return true;
        }

        return $task->assignee_id === $user->id;
    }

    /**
     * Deactivation reuses create_edit_tasks rather than a separate
     * permission slug — same "no distinct capability" precedent as
     * Projects, where closing a project is just part of the normal edit
     * permission, not its own toggle.
     */
    public function delete(User $user, Task $task): bool
    {
        if (! $user->hasPermission('create_edit_tasks', $task->organization_id)) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($task->organization_id);
    }
}
