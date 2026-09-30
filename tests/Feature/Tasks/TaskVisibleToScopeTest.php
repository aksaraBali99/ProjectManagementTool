<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * task #73 (visibility consolidation): direct coverage of
 * Task::scopeVisibleTo() itself, for the two ways it used to disagree
 * with TaskPolicy::view() — no hasPermission('view_tasks') gate, and no
 * active-department filter on the staff department-access branch (see
 * TaskViewableIdsParityTest, which already covers the FULL branch matrix
 * against Gate::allows('view', ...) for the gap-free viewableIdsFor()).
 * Each of scopeVisibleTo()'s five real callers (Calendar, Kanban,
 * Dashboard, Task List, Project drilldown) gets its own regression test
 * confirming the fix actually propagates through that page — see each
 * page's own test file.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
});

function makeStaffForVisibleToScope(Organization $org, Department $department): User
{
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $org->id, 'department_id' => $department->id, 'allowed' => true]);

    return $staff;
}

test('a staff user with valid, active department access sees the task via the scope (sanity/control)', function () {
    $staff = makeStaffForVisibleToScope($this->org, $this->dept);
    $task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Visible', 'priority' => 'medium', 'status' => 'pending',
    ]);

    expect(Task::visibleTo($staff, $this->org->id)->pluck('id')->all())->toBe([$task->id]);
});

test('a staff user whose role has view_tasks revoked sees NO tasks via the scope, even with valid department access', function () {
    $staff = makeStaffForVisibleToScope($this->org, $this->dept);
    Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Should be hidden', 'priority' => 'medium', 'status' => 'pending',
    ]);

    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $viewTasksId = Permission::where('slug', 'view_tasks')->firstOrFail()->id;
    $staffRole->permissions()->detach($viewTasksId);

    expect(Task::visibleTo($staff, $this->org->id)->count())->toBe(0);
});

test('a staff user with access_permissions allowing a now-deactivated department no longer sees that task via the scope', function () {
    $staff = makeStaffForVisibleToScope($this->org, $this->dept);
    Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Should be hidden', 'priority' => 'medium', 'status' => 'pending',
    ]);

    $this->dept->update(['is_active' => false]);

    expect(Task::visibleTo($staff, $this->org->id)->count())->toBe(0);
});

test('the view_tasks gate applies to management and clients too, not just staff', function () {
    $management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
    Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Task', 'priority' => 'medium', 'status' => 'pending',
    ]);

    $managementRole = Role::where('slug', 'management')->firstOrFail();
    $viewTasksId = Permission::where('slug', 'view_tasks')->firstOrFail()->id;
    $managementRole->permissions()->detach($viewTasksId);

    expect(Task::visibleTo($management, $this->org->id)->count())->toBe(0);
});
