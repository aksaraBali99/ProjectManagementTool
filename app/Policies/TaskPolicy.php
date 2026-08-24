<?php

namespace App\Policies;

use App\Models\Subtask;
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

            if (Subtask::whereHas('task', fn ($query) => $query->where('organization_id', $organizationId))
                ->where('assignee_id', $user->id)
                ->exists()) {
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

        if ($task->subtasks()->where('assignee_id', $user->id)->exists()) {
            return true;
        }

        return $user->isClientOnProject($task->project_id);
    }

    /**
     * $departmentId is only known once a department's actually been picked
     * (store()'s server-side check) — page-load / button-visibility calls
     * omit it and fall back to "does this staff member have ANY department
     * grant in this org", since the specific department isn't chosen yet.
     */
    public function create(User $user, int $organizationId, ?int $departmentId = null): bool
    {
        if (! $user->hasPermission('create_edit_tasks', $organizationId)) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organizationId)) {
            return true;
        }

        if (! $user->isStaffInOrg($organizationId)) {
            return false;
        }

        return $departmentId !== null
            ? $user->hasDepartmentAccess($organizationId, $departmentId)
            : $user->allowedDepartmentIds($organizationId)->isNotEmpty();
    }

    /**
     * Three independent ways in: management-tier (gated by create_edit_tasks),
     * staff holding create_edit_tasks AND department access to the task's
     * own department, or being the task's assignee (an identity/ownership
     * check, not a role capability — deliberately NOT gated by
     * create_edit_tasks, since staff without that permission must still be
     * able to edit tasks assigned to them).
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasPermission('create_edit_tasks', $task->organization_id)) {
            if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($task->organization_id)) {
                return true;
            }

            if ($user->isStaffInOrg($task->organization_id) && $user->hasDepartmentAccess($task->organization_id, $task->department_id)) {
                return true;
            }
        }

        return $task->assignee_id === $user->id;
    }

    /**
     * Kanban's card-move (drag-and-drop or the dropdown fallback) is
     * deliberately its own capability, not a reuse of create_edit_tasks —
     * same two-way-in shape as update() (management-tier via permission,
     * OR being the task's own assignee), so an assignee can still move
     * their own card even if update_kanban_cards isn't granted to their
     * role, same reasoning as update()'s assignee bypass.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        if ($user->hasPermission('update_kanban_cards', $task->organization_id)
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
