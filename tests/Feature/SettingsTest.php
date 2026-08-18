<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

test('an authenticated user can reach the settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/settings')->assertOk();
});

test('an owner sees the manage users link on the settings page', function () {
    $role = Role::create(['name' => 'Owner', 'slug' => 'owner', 'is_system' => true]);
    $owner = User::factory()->create();
    $owner->roles()->attach($role->id);

    $response = $this->actingAs($owner)->get('/settings');

    $response->assertOk();
    $response->assertSee('Manage users');
});

test('a non-admin does not see the manage users link on the settings page', function () {
    $role = Role::create(['name' => 'Management', 'slug' => 'management', 'is_system' => true]);
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $organization->id, 'user_id' => $manager->id, 'role_id' => $role->id]);

    $response = $this->actingAs($manager)->get('/settings');

    $response->assertOk();
    $response->assertDontSee('Manage users');
});
