<?php

namespace App\Policies;

use App\Models\Document;
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

    /**
     * task #73 phase 2: unlinking (detaching) an already-attached document
     * — deliberately NOT the same gate as update() (full task-edit
     * rights). manage_documents + being able to see the task at all is
     * the whole rule, so a manage_documents holder with no task-edit
     * permission at all (or a Client attaching/removing documents on
     * their own project's task, once Phase 3/4 need that) can still do
     * this. TaskDocumentController::detach() and the Task edit page's own
     * Unlink/Detach button both call this exact method, so they can never
     * disagree.
     */
    public function unlinkDocuments(User $user, Task $task): bool
    {
        return $user->hasPermission('manage_documents', $task->organization_id) && $this->view($user, $task);
    }

    /**
     * Detaching one SPECIFIC document, which unlinkDocuments() above
     * can't answer: it's task-scoped only and never sees the document, so
     * any manage_documents holder who could view the task could detach
     * anyone else's — a Staff member removing a document management
     * attached, or a Client removing a Public document staff attached to
     * their project's task.
     *
     * Adds the ownership half DocumentPolicy::canManage() already applies
     * to rename/move/delete on the Documents page: the management tier
     * overrides, otherwise you must be the uploader. There is no
     * "manage all documents" permission in PermissionSeeder, so the
     * management tier IS the override, exactly as it is there.
     *
     * unlinkDocuments() itself is deliberately unchanged: folder detach
     * (TaskFolderController::detach()) and the "Attach folder" button
     * still use it, and folders are out of scope here.
     */
    public function detachDocument(User $user, Task $task, Document $document): bool
    {
        if (! $this->unlinkDocuments($user, $task)) {
            return false;
        }

        return $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($task->organization_id)
            || $document->uploaded_by === $user->id;
    }

    /**
     * task #73 phase 3: the picker's own gate — the button, the picker
     * list endpoint, and the attach endpoint all share this exact check,
     * so they can never disagree. Same shape as unlinkDocuments() above
     * (manage_documents + can view the task) plus one addition: a
     * Client-role user is excluded unconditionally, even one an owner
     * granted manage_documents to. This is deliberately NOT the same
     * exclusion unlinkDocuments() has (it has none) — Client access to
     * unlinking wasn't part of this phase's brief, only attaching.
     *
     * This only covers the task-side ability ("can this user manage
     * documents on this task at all"). The document-side eligibility for
     * an actual attach (view() on the specific document, same company,
     * not private) is checked separately in the attach endpoint itself,
     * against the real DocumentPolicy::view() — never against this
     * method or the picker's own narrower query.
     */
    public function attachDocuments(User $user, Task $task): bool
    {
        if ($user->isClientInOrg($task->organization_id)) {
            return false;
        }

        return $user->hasPermission('manage_documents', $task->organization_id) && $this->view($user, $task);
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
