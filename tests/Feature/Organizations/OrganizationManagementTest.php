<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::create(['name' => 'Owner', 'slug' => 'owner', 'is_system' => true])->id);

    Role::create(['name' => 'Management', 'slug' => 'management', 'is_system' => true]);
});

test('a non-owner/non-super-admin cannot access organization management', function () {
    $manager = User::factory()->create();
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
    OrgMember::create([
        'organization_id' => $organization->id,
        'user_id' => $manager->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->actingAs($manager)->get('/organizations')->assertForbidden();
    $this->actingAs($manager)->get('/organizations/create')->assertForbidden();
});

test('an owner can create a company', function () {
    $response = $this->actingAs($this->owner)->post('/organizations', [
        'name' => 'New Co',
        'slug' => 'new-co',
        'accent_color' => '#1D9E75',
    ]);

    $response->assertRedirect('/organizations');
    $this->assertDatabaseHas('organizations', ['slug' => 'new-co']);
});

test('creating a company requires a valid hex accent color', function () {
    $response = $this->actingAs($this->owner)->post('/organizations', [
        'name' => 'New Co',
        'slug' => 'new-co',
        'accent_color' => 'not-a-color',
    ]);

    $response->assertSessionHasErrors('accent_color');
    $this->assertDatabaseMissing('organizations', ['slug' => 'new-co']);
});

test('an owner can update a company', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $response = $this->actingAs($this->owner)->put("/organizations/{$organization->id}", [
        'name' => 'Org A Renamed',
        'slug' => 'org-a',
        'accent_color' => '#534AB7',
    ]);

    $response->assertRedirect('/organizations');
    expect($organization->fresh()->name)->toBe('Org A Renamed');
});

test('deleting a company requires typing its exact name', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $wrong = $this->actingAs($this->owner)->delete("/organizations/{$organization->id}", [
        'confirm_name' => 'Wrong Name',
    ]);
    $wrong->assertSessionHasErrors('confirm_name');
    $this->assertDatabaseHas('organizations', ['id' => $organization->id]);

    $right = $this->actingAs($this->owner)->delete("/organizations/{$organization->id}", [
        'confirm_name' => 'Org A',
    ]);
    $right->assertRedirect('/organizations');
    $this->assertDatabaseMissing('organizations', ['id' => $organization->id]);
});

test('deleting a company cascades to its departments', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $department = Department::create(['organization_id' => $organization->id, 'name' => 'Marketing', 'color' => '#000000']);

    $this->actingAs($this->owner)->delete("/organizations/{$organization->id}", [
        'confirm_name' => 'Org A',
    ]);

    $this->assertDatabaseMissing('departments', ['id' => $department->id]);
});
