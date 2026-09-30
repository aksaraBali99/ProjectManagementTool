<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * task #70 (unified eligibility): Task::eligibleAssigneesFor() is now the
 * ONE shared rule behind the Assignee dropdown (BuildsAssigneeOptions),
 * the @mention autocomplete's base set (Task::viewableUsers()), and
 * ValidatesTaskAssignment::isAssignableStaffForProject() (the server-side
 * acceptance check). These tests exercise the shared method directly,
 * plus the single most important guarantee of unifying it: the Assignee
 * dropdown and mention autocomplete return IDENTICAL sets for the same
 * task.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
});

function makeStaffForEligibility(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    return $staff;
}

function grantDepartmentAccessForEligibility(User $user, Organization $org, Department $department): void
{
    AccessPermission::create(['user_id' => $user->id, 'organization_id' => $org->id, 'department_id' => $department->id, 'allowed' => true]);
}

test('super_admin, owner, and management-in-org are eligible unconditionally, with no project or department attachment', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id);

    $management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $eligibleIds = Task::eligibleAssigneesFor($this->project, $this->dept->id)->pluck('id')->all();

    expect($eligibleIds)->toContain($this->owner->id)
        ->toContain($superAdmin->id)
        ->toContain($management->id);
});

test('a project\'s client is eligible', function () {
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);
    $this->project->clients()->attach($client->id);

    $eligibleIds = Task::eligibleAssigneesFor($this->project, $this->dept->id)->pluck('id')->all();

    expect($eligibleIds)->toContain($client->id);
});

test('a user who is a client but NOT attached to this specific project is not eligible', function () {
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);
    // Deliberately not attached to $this->project.

    $eligibleIds = Task::eligibleAssigneesFor($this->project, $this->dept->id)->pluck('id')->all();

    expect($eligibleIds)->not->toContain($client->id);
});

test('a staff member with BOTH project_staff and active department access is eligible', function () {
    $staff = makeStaffForEligibility($this->org);
    $this->project->staff()->attach($staff->id);
    grantDepartmentAccessForEligibility($staff, $this->org, $this->dept);

    $eligibleIds = Task::eligibleAssigneesFor($this->project, $this->dept->id)->pluck('id')->all();

    expect($eligibleIds)->toContain($staff->id);
});

test('task #70 (unified eligibility): a staff member with project_staff but WITHOUT department access is no longer eligible', function () {
    // This is the deliberate behavior change — previously assignable
    // under the old project-membership-alone rule, no longer is.
    $staff = makeStaffForEligibility($this->org);
    $this->project->staff()->attach($staff->id);

    $eligibleIds = Task::eligibleAssigneesFor($this->project, $this->dept->id)->pluck('id')->all();

    expect($eligibleIds)->not->toContain($staff->id);
});

test('a staff member with active department access but WITHOUT project_staff is not eligible', function () {
    $staff = makeStaffForEligibility($this->org);
    grantDepartmentAccessForEligibility($staff, $this->org, $this->dept);

    $eligibleIds = Task::eligibleAssigneesFor($this->project, $this->dept->id)->pluck('id')->all();

    expect($eligibleIds)->not->toContain($staff->id);
});

test('a staff member with BOTH conditions but to a DEACTIVATED department is not eligible', function () {
    $staff = makeStaffForEligibility($this->org);
    $this->project->staff()->attach($staff->id);
    grantDepartmentAccessForEligibility($staff, $this->org, $this->dept);
    $this->dept->update(['is_active' => false]);

    $eligibleIds = Task::eligibleAssigneesFor($this->project, $this->dept->id)->pluck('id')->all();

    expect($eligibleIds)->not->toContain($staff->id);
});

test('the Assignee dropdown and mention autocomplete return IDENTICAL eligible user sets for the same task', function () {
    $eligibleStaff = makeStaffForEligibility($this->org);
    $this->project->staff()->attach($eligibleStaff->id);
    grantDepartmentAccessForEligibility($eligibleStaff, $this->org, $this->dept);

    $ineligibleStaff = makeStaffForEligibility($this->org);
    $this->project->staff()->attach($ineligibleStaff->id);
    // No department access granted — ineligible for both.

    $management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);
    $this->project->clients()->attach($client->id);

    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Shared eligibility task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    // The Assignee dropdown's own source, direct.
    $assigneeDropdownIds = Task::eligibleAssigneesFor($task->project, $task->department_id)
        ->pluck('id')->sort()->values()->all();

    // The mention autocomplete's source — no current assignee/subtask
    // assignee on this fresh task, so viewableUsers() adds nothing beyond
    // eligibleAssigneesFor() itself; the two sets should be identical.
    $mentionIds = $task->viewableUsers()->pluck('id')->sort()->values()->all();

    expect($assigneeDropdownIds)->not->toBeEmpty();
    expect($mentionIds)->toBe($assigneeDropdownIds);

    // And the actual, meaningful membership: eligible staff, management,
    // client, and the owner (global role) all present; the project_staff-
    // without-department-access staff member absent from both.
    expect($assigneeDropdownIds)->toContain($eligibleStaff->id, $management->id, $client->id, $this->owner->id)
        ->not->toContain($ineligibleStaff->id);
});
