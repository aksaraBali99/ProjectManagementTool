<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::create(['name' => 'Owner', 'slug' => 'owner', 'is_system' => true])->id);

    $this->organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
});

test('a non-owner/non-super-admin cannot access department management', function () {
    Role::create(['name' => 'Management', 'slug' => 'management', 'is_system' => true]);
    $manager = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->organization->id,
        'user_id' => $manager->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->actingAs($manager)->get('/departments')->assertForbidden();
});

test('an owner can create a department for a company', function () {
    $response = $this->actingAs($this->owner)->post('/departments', [
        'organization_id' => $this->organization->id,
        'name' => 'Marketing',
        'color' => '#EEEDFE',
    ]);

    $response->assertRedirect('/departments/'.$this->organization->id);
    $this->assertDatabaseHas('departments', ['name' => 'Marketing', 'organization_id' => $this->organization->id]);
});

test('department names must be unique within the same company but not across companies', function () {
    $otherOrg = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);
    Department::create(['organization_id' => $this->organization->id, 'name' => 'Marketing', 'color' => '#000000']);

    $duplicate = $this->actingAs($this->owner)->post('/departments', [
        'organization_id' => $this->organization->id,
        'name' => 'Marketing',
        'color' => '#EEEDFE',
    ]);
    $duplicate->assertSessionHasErrors('name');

    $differentOrg = $this->actingAs($this->owner)->post('/departments', [
        'organization_id' => $otherOrg->id,
        'name' => 'Marketing',
        'color' => '#EEEDFE',
    ]);
    $differentOrg->assertSessionHasNoErrors();
    $this->assertDatabaseHas('departments', ['name' => 'Marketing', 'organization_id' => $otherOrg->id]);
});

test('there is no route to delete a department', function () {
    $department = Department::create(['organization_id' => $this->organization->id, 'name' => 'Marketing', 'color' => '#000000']);

    $this->actingAs($this->owner)->delete("/departments/{$department->id}")->assertStatus(405);
});

test('an owner can deactivate and reactivate a department without deleting it, even with tasks assigned', function () {
    $department = Department::create(['organization_id' => $this->organization->id, 'name' => 'Marketing', 'color' => '#000000']);
    $project = Project::create([
        'organization_id' => $this->organization->id,
        'name' => 'Proj',
        'description' => 'd',
        'client_name' => 'c',
    ]);
    Task::create([
        'organization_id' => $this->organization->id,
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => 'A task',
    ]);

    $this->actingAs($this->owner)->patch("/departments/{$department->id}/toggle-active");
    expect($department->fresh()->is_active)->toBeFalse();
    $this->assertDatabaseHas('departments', ['id' => $department->id]);

    $this->actingAs($this->owner)->patch("/departments/{$department->id}/toggle-active");
    expect($department->fresh()->is_active)->toBeTrue();
});

test('deactivating a department removes a staff member\'s access to it immediately', function () {
    $role = Role::create(['name' => 'Staff', 'slug' => 'staff', 'is_system' => true]);
    $department = Department::create(['organization_id' => $this->organization->id, 'name' => 'Marketing', 'color' => '#000000']);
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->organization->id, 'user_id' => $staff->id, 'role_id' => $role->id]);
    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $this->organization->id,
        'department_id' => $department->id,
        'allowed' => true,
    ]);

    expect($staff->hasDepartmentAccess($this->organization->id, $department->id))->toBeTrue();

    $this->actingAs($this->owner)->patch("/departments/{$department->id}/toggle-active");

    expect($staff->hasDepartmentAccess($this->organization->id, $department->id))->toBeFalse();
});

test('an owner still sees an inactive department in the admin list', function () {
    Department::create(['organization_id' => $this->organization->id, 'name' => 'Marketing', 'color' => '#000000', 'is_active' => false]);

    $response = $this->actingAs($this->owner)->get('/departments');

    $response->assertOk();
    $response->assertSee('Marketing');
});
