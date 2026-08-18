<?php

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

    $response->assertRedirect('/departments');
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

test('a department with tasks cannot be deleted', function () {
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

    $response = $this->actingAs($this->owner)->delete("/departments/{$department->id}");

    $response->assertSessionHasErrors('department');
    $this->assertDatabaseHas('departments', ['id' => $department->id]);
});

test('a department with no tasks can be deleted', function () {
    $department = Department::create(['organization_id' => $this->organization->id, 'name' => 'Marketing', 'color' => '#000000']);

    $response = $this->actingAs($this->owner)->delete("/departments/{$department->id}");

    $response->assertRedirect('/departments');
    $this->assertDatabaseMissing('departments', ['id' => $department->id]);
});
