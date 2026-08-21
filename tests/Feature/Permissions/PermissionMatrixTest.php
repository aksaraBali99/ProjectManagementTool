<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

test('a non-owner/non-super-admin cannot access the permission matrix page', function () {
    $this->actingAs($this->management)->get('/roles/permissions')->assertForbidden();
    $this->actingAs($this->management)->put('/roles/permissions', [])->assertForbidden();
});

test('an owner can view the permission matrix with current grants pre-checked', function () {
    $response = $this->actingAs($this->owner)->get('/roles/permissions');

    $response->assertOk();
    $response->assertSee('Create & edit tasks');
    $response->assertSee('Locked');
});

test('the permission matrix lists the Dashboard and Kanban board view permissions', function () {
    $response = $this->actingAs($this->owner)->get('/roles/permissions');

    $response->assertOk();
    $response->assertSee('View dashboard');
    $response->assertSee('View kanban board');
});

test('comment permissions are grouped under Tasks, not a separate Comments group', function () {
    $response = $this->actingAs($this->owner)->get('/roles/permissions');

    $response->assertOk();
    $response->assertSee('View comments');
    $response->assertSee('Add / edit own comments');
    $response->assertDontSee('Comments');
});

test('unchecking a permission for a role in the matrix removes that capability immediately', function () {
    $project = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Project A',
        'description' => 'd',
    ]);
    $department = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);

    // Sanity check: management can create a task before the change.
    $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => 'Before revoking',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    $managementRole = Role::where('slug', 'management')->firstOrFail();
    $remainingPermissionIds = $managementRole->permissions()
        ->where('slug', '!=', 'create_edit_tasks')
        ->pluck('permissions.id')
        ->all();

    $this->actingAs($this->owner)->put('/roles/permissions', [
        'role_permissions' => [
            $managementRole->id => $remainingPermissionIds,
        ],
    ])->assertRedirect();

    expect($managementRole->fresh()->permissions()->pluck('slug')->all())->not->toContain('create_edit_tasks');

    $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => 'After revoking',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertForbidden();

    $this->actingAs($this->management)->get("/tasks/create/{$project->id}")->assertForbidden();
});

test('super_admin and owner columns cannot be modified through the matrix, even via a direct form submission attempt', function () {
    $superAdminRole = Role::where('slug', 'super_admin')->firstOrFail();
    $ownerRole = Role::where('slug', 'owner')->firstOrFail();
    $originalSuperAdminPermissions = $superAdminRole->permissions()->pluck('permissions.id')->sort()->values()->all();
    $originalOwnerPermissions = $ownerRole->permissions()->pluck('permissions.id')->sort()->values()->all();

    $onePermissionId = Permission::first()->id;

    $this->actingAs($this->owner)->put('/roles/permissions', [
        'role_permissions' => [
            $superAdminRole->id => [$onePermissionId],
            $ownerRole->id => [$onePermissionId],
        ],
    ])->assertRedirect();

    expect($superAdminRole->fresh()->permissions()->pluck('permissions.id')->sort()->values()->all())->toBe($originalSuperAdminPermissions);
    expect($ownerRole->fresh()->permissions()->pluck('permissions.id')->sort()->values()->all())->toBe($originalOwnerPermissions);
});
