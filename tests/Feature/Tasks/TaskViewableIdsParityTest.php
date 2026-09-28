<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

/**
 * task #73 phase 2: Task::viewableIdsFor() is a batched, no-N+1 stand-in
 * for calling Gate::allows('view', $task) per task — this is the
 * regression guard asserting the two NEVER disagree, across a matrix
 * covering every branch TaskPolicy::view() itself has, plus the two
 * cases scopeVisibleTo() is known to get wrong (a deactivated department,
 * and a revoked view_tasks permission) specifically because
 * viewableIdsFor() is NOT built on that scope.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function makeManagementForParity(Organization $org): User
{
    $user = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $user->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    return $user;
}

function makeStaffForParity(Organization $org): User
{
    $user = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $user->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    return $user;
}

function makeClientForParity(Organization $org): User
{
    $user = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $user->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);

    return $user;
}

function grantDepartmentAccessForParity(User $user, Organization $org, Department $dept): void
{
    AccessPermission::create([
        'user_id' => $user->id,
        'organization_id' => $org->id,
        'department_id' => $dept->id,
        'allowed' => true,
    ]);
}

/** Asserts Gate::allows('view', $task) and viewableIdsFor() agree, and both equal $expected. */
function assertViewParity(User $user, Task $task, bool $expected, string $label): void
{
    $gateResult = Gate::forUser($user)->allows('view', $task);
    $batchedResult = Task::viewableIdsFor($user, [$task->id])->contains($task->id);

    expect($gateResult)->toBe($expected, "TaskPolicy::view() for [{$label}] expected {$expected}");
    expect($batchedResult)->toBe($expected, "Task::viewableIdsFor() for [{$label}] expected {$expected}");
    expect($batchedResult)->toBe($gateResult, "Mismatch between TaskPolicy::view() and Task::viewableIdsFor() for [{$label}]");
}

test('super_admin can always view', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id);

    assertViewParity($superAdmin, $this->task, true, 'super_admin');
});

test('owner can always view', function () {
    assertViewParity($this->owner, $this->task, true, 'owner');
});

test('management in the company can always view', function () {
    $management = makeManagementForParity($this->org);

    assertViewParity($management, $this->task, true, 'management');
});

test('staff with active-department access can view', function () {
    $staff = makeStaffForParity($this->org);
    grantDepartmentAccessForParity($staff, $this->org, $this->dept);

    assertViewParity($staff, $this->task, true, 'staff, active department access');
});

test('staff with department access to a DEACTIVATED department cannot view', function () {
    $staff = makeStaffForParity($this->org);
    grantDepartmentAccessForParity($staff, $this->org, $this->dept);
    $this->dept->update(['is_active' => false]);

    assertViewParity($staff, $this->task, false, 'staff, deactivated department');
});

test('staff with no department access at all cannot view', function () {
    $staff = makeStaffForParity($this->org);

    assertViewParity($staff, $this->task, false, 'staff, no department access');
});

test('staff assigned directly to the task can view it even outside their granted departments', function () {
    $staff = makeStaffForParity($this->org);
    $this->task->update(['assignee_id' => $staff->id]);

    assertViewParity($staff, $this->task, true, 'staff, direct assignee');
});

test('staff assigned to a subtask can view the parent task even outside their granted departments', function () {
    $staff = makeStaffForParity($this->org);
    Subtask::create(['task_id' => $this->task->id, 'title' => 'Sub', 'assignee_id' => $staff->id]);

    assertViewParity($staff, $this->task, true, 'staff, subtask assignee');
});

test('staff with view_tasks revoked cannot view, even with valid department access', function () {
    $staff = makeStaffForParity($this->org);
    grantDepartmentAccessForParity($staff, $this->org, $this->dept);

    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $viewTasksId = Permission::where('slug', 'view_tasks')->firstOrFail()->id;
    $staffRole->permissions()->detach($viewTasksId);

    assertViewParity($staff, $this->task, false, 'staff, view_tasks revoked');
});

test('a client attached to the task\'s project can view', function () {
    $client = makeClientForParity($this->org);
    $this->project->clients()->attach($client->id);

    assertViewParity($client, $this->task, true, 'client, attached project');
});

test('a client not attached to the task\'s project cannot view', function () {
    $client = makeClientForParity($this->org);
    $otherProject = Project::create(['organization_id' => $this->org->id, 'name' => 'Other project', 'description' => 'd']);
    $otherProject->clients()->attach($client->id);

    assertViewParity($client, $this->task, false, 'client, unattached project');
});

test('a user with no membership in the company at all cannot view', function () {
    $outsider = User::factory()->create();

    assertViewParity($outsider, $this->task, false, 'outsider');
});

test('viewableIdsFor() batches across multiple tasks and organizations in one call, still agreeing with the gate per task', function () {
    $orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);
    $deptB = Department::create(['organization_id' => $orgB->id, 'name' => 'Ops', 'color' => '#111111']);
    $projectB = Project::create(['organization_id' => $orgB->id, 'name' => 'Project B', 'description' => 'd']);
    $taskB = Task::create([
        'organization_id' => $orgB->id, 'project_id' => $projectB->id, 'department_id' => $deptB->id,
        'title' => 'Task B', 'priority' => 'medium', 'status' => 'pending',
    ]);

    $management = makeManagementForParity($this->org);
    // Not a member of Org B at all.

    $viewableIds = Task::viewableIdsFor($management, [$this->task->id, $taskB->id]);

    expect($viewableIds->contains($this->task->id))->toBe(Gate::forUser($management)->allows('view', $this->task));
    expect($viewableIds->contains($taskB->id))->toBe(Gate::forUser($management)->allows('view', $taskB));
    expect($viewableIds->all())->toBe([$this->task->id]);
});

test('viewableIdsFor() returns an empty collection for an empty input without querying tasks', function () {
    expect(Task::viewableIdsFor($this->owner, []))->toBeInstanceOf(Collection::class);
    expect(Task::viewableIdsFor($this->owner, [])->isEmpty())->toBeTrue();
});
