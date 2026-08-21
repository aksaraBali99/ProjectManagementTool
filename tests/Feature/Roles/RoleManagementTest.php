<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::create(['name' => 'Owner', 'slug' => 'owner', 'is_system' => true])->id);
});

test('a non-owner/non-super-admin cannot access role management', function () {
    $role = Role::create(['name' => 'Management', 'slug' => 'management', 'is_system' => true]);
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $organization->id, 'user_id' => $manager->id, 'role_id' => $role->id]);

    $this->actingAs($manager)->get('/roles')->assertForbidden();
});

test('an owner can edit a system role\'s name and description but not its slug', function () {
    $role = Role::create(['name' => 'Staff', 'slug' => 'staff', 'description' => 'old', 'is_system' => true]);

    $response = $this->actingAs($this->owner)->put("/roles/{$role->id}", [
        'name' => 'Staff Member',
        'slug' => 'hacked-slug',
        'description' => 'new description',
    ]);

    $response->assertSessionHasErrors('slug');
    expect($role->fresh()->slug)->toBe('staff');
});

test('an owner can edit a system role\'s name and description when slug is left unchanged', function () {
    $role = Role::create(['name' => 'Staff', 'slug' => 'staff', 'description' => 'old', 'is_system' => true]);

    $response = $this->actingAs($this->owner)->put("/roles/{$role->id}", [
        'name' => 'Staff Member',
        'slug' => 'staff',
        'description' => 'new description',
    ]);

    $response->assertRedirect('/roles');
    expect($role->fresh()->name)->toBe('Staff Member')
        ->and($role->fresh()->description)->toBe('new description')
        ->and($role->fresh()->slug)->toBe('staff');
});

test('there is no route to create or delete a role', function () {
    $this->actingAs($this->owner)->post('/roles', ['name' => 'Custom'])->assertStatus(405);
});

test('the slug is not shown as a column or field anywhere on the roles pages', function () {
    $role = Role::create(['name' => 'Staff', 'slug' => 'staff', 'description' => 'd', 'is_system' => true]);

    $this->actingAs($this->owner)->get('/roles')
        ->assertOk()
        ->assertDontSee('Slug');

    $this->actingAs($this->owner)->get("/roles/{$role->id}/edit")
        ->assertOk()
        ->assertDontSee('name="slug"', false)
        ->assertDontSee('Slug');
});
