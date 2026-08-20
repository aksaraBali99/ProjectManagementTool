<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->owner = Role::create(['name' => 'Owner', 'slug' => 'owner', 'is_system' => true]);
    $this->management = Role::create(['name' => 'Management', 'slug' => 'management', 'is_system' => true]);
    $this->staff = Role::create(['name' => 'Staff', 'slug' => 'staff', 'is_system' => true]);

    $this->viewTasks = Permission::create(['slug' => 'view_tasks', 'name' => 'View tasks', 'group' => 'Tasks']);
    $this->createEditTasks = Permission::create(['slug' => 'create_edit_tasks', 'name' => 'Create & edit tasks', 'group' => 'Tasks']);

    $this->owner->permissions()->sync([$this->viewTasks->id, $this->createEditTasks->id]);
    $this->management->permissions()->sync([$this->viewTasks->id, $this->createEditTasks->id]);
    $this->staff->permissions()->sync([$this->viewTasks->id]);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);
});

test('a global role grants a permission regardless of organization context', function () {
    $user = User::factory()->create();
    $user->roles()->attach($this->owner->id);

    expect($user->hasPermission('view_tasks'))->toBeTrue();
    expect($user->hasPermission('view_tasks', $this->orgA->id))->toBeTrue();
    expect($user->hasPermission('view_tasks', $this->orgB->id))->toBeTrue();
});

test('a user with no matching role does not have the permission', function () {
    $user = User::factory()->create();

    expect($user->hasPermission('view_tasks'))->toBeFalse();
});

test('a per-organization role grants a permission only when checked against that organization', function () {
    $user = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $user->id, 'role_id' => $this->management->id]);

    expect($user->hasPermission('view_tasks', $this->orgA->id))->toBeTrue();
    expect($user->hasPermission('view_tasks', $this->orgB->id))->toBeFalse();
    expect($user->hasPermission('view_tasks'))->toBeFalse();
});

test('a per-organization role only grants the specific permissions assigned to it', function () {
    $user = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $user->id, 'role_id' => $this->staff->id]);

    expect($user->hasPermission('view_tasks', $this->orgA->id))->toBeTrue();
    expect($user->hasPermission('create_edit_tasks', $this->orgA->id))->toBeFalse();
});

test('holding a different role in a different organization does not leak permissions across organizations', function () {
    $user = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $user->id, 'role_id' => $this->management->id]);
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $user->id, 'role_id' => $this->staff->id]);

    expect($user->hasPermission('create_edit_tasks', $this->orgA->id))->toBeTrue();
    expect($user->hasPermission('create_edit_tasks', $this->orgB->id))->toBeFalse();
});

test('any applicable role granting the permission is enough, even if another applicable role would not grant it', function () {
    $user = User::factory()->create();
    $user->roles()->attach($this->owner->id);
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $user->id, 'role_id' => $this->staff->id]);

    // Global "owner" grants create_edit_tasks; the org-specific "staff" role
    // in orgA does not — the user should still pass, since ANY applicable
    // role granting the permission is sufficient.
    expect($user->hasPermission('create_edit_tasks', $this->orgA->id))->toBeTrue();
});
