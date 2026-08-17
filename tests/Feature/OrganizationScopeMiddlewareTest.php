<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'org.scope'])
        ->get('/_test/organizations/{organization}', fn () => 'ok');
});

test('a user who belongs to the organization can pass the middleware', function () {
    $role = Role::create(['name' => 'Management', 'slug' => 'management', 'is_system' => true]);
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
    $user = User::factory()->create();
    OrgMember::create(['organization_id' => $organization->id, 'user_id' => $user->id, 'role_id' => $role->id]);

    $this->actingAs($user)
        ->get("/_test/organizations/{$organization->id}")
        ->assertOk();
});

test('a user who does not belong to the organization is forbidden', function () {
    $organization = Organization::create(['name' => 'Org B', 'slug' => 'org-b']);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get("/_test/organizations/{$organization->id}")
        ->assertForbidden();
});

test('super admin passes the middleware for any organization', function () {
    $role = Role::create(['name' => 'Super Admin', 'slug' => 'super_admin', 'is_system' => true]);
    $organization = Organization::create(['name' => 'Org C', 'slug' => 'org-c']);
    $user = User::factory()->create();
    $user->roles()->attach($role->id);

    $this->actingAs($user)
        ->get("/_test/organizations/{$organization->id}")
        ->assertOk();
});
