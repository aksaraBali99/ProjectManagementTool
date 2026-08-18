<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

test('an owner sees Users nested under Settings in the sidebar', function () {
    $role = Role::create(['name' => 'Owner', 'slug' => 'owner', 'is_system' => true]);
    $owner = User::factory()->create();
    $owner->roles()->attach($role->id);

    $response = $this->actingAs($owner)->get('/dashboard');

    $response->assertOk();
    $response->assertSee('Settings');
    $response->assertSee('Users');
});

test('a non-admin does not see Users or Settings in the sidebar', function () {
    $role = Role::create(['name' => 'Management', 'slug' => 'management', 'is_system' => true]);
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $organization->id, 'user_id' => $manager->id, 'role_id' => $role->id]);

    $response = $this->actingAs($manager)->get('/dashboard');

    $response->assertOk();
    $response->assertDontSee('Settings');
});

test('the settings route no longer exists', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/settings')->assertNotFound();
});
