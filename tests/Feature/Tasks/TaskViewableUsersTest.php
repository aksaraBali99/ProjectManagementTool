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
 * Task::viewableUsers() is the reverse of TaskPolicy::view(): given a
 * task, who can see it, rather than given a user, can they see this
 * task. Built to fix a permission leak in the comment @mention
 * autocomplete (see CommentMentionTest.php /
 * CommentMentionEligibilityTest.php for the mention-specific coverage);
 * these tests exercise the method itself directly, one TaskPolicy::view()
 * branch at a time, since it's meant to be reusable beyond mentions.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->otherDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Sales', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function makeStaffMember(Organization $org, ?Department $department = null): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    if ($department) {
        AccessPermission::create([
            'user_id' => $staff->id,
            'organization_id' => $org->id,
            'department_id' => $department->id,
            'allowed' => true,
        ]);
    }

    return $staff;
}

test('the owner (global role) is always included', function () {
    expect($this->task->viewableUsers()->pluck('id')->all())->toContain($this->owner->id);
});

test('management in the task\'s org is included, with no department access needed', function () {
    $management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    expect($this->task->viewableUsers()->pluck('id')->all())->toContain($management->id);
});

test('a staff member WITH access to the task\'s own department is included', function () {
    $staff = makeStaffMember($this->org, $this->dept);

    expect($this->task->viewableUsers()->pluck('id')->all())->toContain($staff->id);
});

test('a staff member WITHOUT access to the task\'s department is excluded, even if attached to the project', function () {
    $staff = makeStaffMember($this->org, $this->otherDept);
    $this->project->staff()->attach($staff->id);

    expect($this->task->viewableUsers()->pluck('id')->all())->not->toContain($staff->id);
});

test('a staff member with no department access at all is excluded', function () {
    $staff = makeStaffMember($this->org);

    expect($this->task->viewableUsers()->pluck('id')->all())->not->toContain($staff->id);
});

test('the task\'s assignee is included even without department access', function () {
    $staff = makeStaffMember($this->org, $this->otherDept);
    $this->task->update(['assignee_id' => $staff->id]);

    expect($this->task->viewableUsers()->pluck('id')->all())->toContain($staff->id);
});

test('a subtask\'s assignee is included even without department access', function () {
    $staff = makeStaffMember($this->org, $this->otherDept);
    $this->task->subtasks()->create(['title' => 'Sub', 'assignee_id' => $staff->id]);

    expect($this->task->viewableUsers()->pluck('id')->all())->toContain($staff->id);
});

test('the project\'s client is included', function () {
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $this->project->clients()->attach($client->id);

    expect($this->task->viewableUsers()->pluck('id')->all())->toContain($client->id);
});

test('a client not attached to this project is excluded', function () {
    $otherProject = Project::create(['organization_id' => $this->org->id, 'name' => 'Other Project', 'description' => 'd']);
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $otherProject->clients()->attach($client->id);

    expect($this->task->viewableUsers()->pluck('id')->all())->not->toContain($client->id);
});

test('a department deactivated after the grant no longer counts, matching hasDepartmentAccess()', function () {
    $staff = makeStaffMember($this->org, $this->dept);
    $this->dept->update(['is_active' => false]);

    expect($this->task->viewableUsers()->pluck('id')->all())->not->toContain($staff->id);
});

test('revoking view_tasks from Staff excludes an otherwise-eligible staff member with department access', function () {
    $staff = makeStaffMember($this->org, $this->dept);

    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $remainingPermissionIds = $staffRole->permissions()->where('slug', '!=', 'view_tasks')->pluck('permissions.id')->all();
    $staffRole->permissions()->sync($remainingPermissionIds);

    expect($this->task->viewableUsers()->pluck('id')->all())->not->toContain($staff->id);
});

test('revoking view_tasks does not affect the owner, whose global role still grants every permission', function () {
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $remainingPermissionIds = $staffRole->permissions()->where('slug', '!=', 'view_tasks')->pluck('permissions.id')->all();
    $staffRole->permissions()->sync($remainingPermissionIds);

    expect($this->task->viewableUsers()->pluck('id')->all())->toContain($this->owner->id);
});

test('an unrelated user with no org membership, no assignment, and no client attachment is excluded', function () {
    $outsider = User::factory()->create();

    expect($this->task->viewableUsers()->pluck('id')->all())->not->toContain($outsider->id);
});
