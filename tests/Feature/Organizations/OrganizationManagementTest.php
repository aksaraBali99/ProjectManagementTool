<?php

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

test('an owner can create a company, and the slug is auto-generated from the name', function () {
    $response = $this->actingAs($this->owner)->post('/organizations', [
        'name' => 'New Co',
        'accent_color' => '#1D9E75',
    ]);

    $response->assertRedirect('/organizations');
    $this->assertDatabaseHas('organizations', ['name' => 'New Co', 'slug' => 'new-co', 'is_active' => true]);
});

test('creating two companies whose names produce the same slug get unique suffixed slugs', function () {
    $this->actingAs($this->owner)->post('/organizations', [
        'name' => 'Acme Co',
        'accent_color' => '#1D9E75',
    ]);
    $this->actingAs($this->owner)->post('/organizations', [
        'name' => 'Acme Co',
        'accent_color' => '#534AB7',
    ]);

    $slugs = Organization::where('name', 'Acme Co')->pluck('slug')->sort()->values();
    expect($slugs->all())->toBe(['acme-co', 'acme-co-2']);
});

test('the slug field is not present on the create or edit company forms', function () {
    $this->actingAs($this->owner)->get('/organizations/create')
        ->assertOk()
        ->assertDontSee('name="slug"', false);

    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->actingAs($this->owner)->get("/organizations/{$organization->id}/edit")
        ->assertOk()
        ->assertDontSee('name="slug"', false);
});

test('creating a company requires a valid hex accent color', function () {
    $response = $this->actingAs($this->owner)->post('/organizations', [
        'name' => 'New Co',
        'accent_color' => 'not-a-color',
    ]);

    $response->assertSessionHasErrors('accent_color');
    $this->assertDatabaseMissing('organizations', ['name' => 'New Co']);
});

test('an owner can update a company, and the slug stays fixed even when the name changes', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $response = $this->actingAs($this->owner)->put("/organizations/{$organization->id}", [
        'name' => 'Org A Renamed',
        'accent_color' => '#534AB7',
    ]);

    $response->assertRedirect('/organizations');
    expect($organization->fresh()->name)->toBe('Org A Renamed')
        ->and($organization->fresh()->slug)->toBe('org-a');
});

test('generateUniqueSlug lowercases, hyphenates, and strips special characters', function () {
    expect(Organization::generateUniqueSlug('Bali Virtual Academy!'))->toBe('bali-virtual-academy');
    expect(Organization::generateUniqueSlug('  R&D / Ops  '))->toBe('rd-ops');
});

test('there is no route to delete a company', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->actingAs($this->owner)->delete("/organizations/{$organization->id}")->assertStatus(405);
});

test('an owner can deactivate and reactivate a company without deleting it', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->actingAs($this->owner)->patch("/organizations/{$organization->id}/toggle-active");
    expect($organization->fresh()->is_active)->toBeFalse();
    $this->assertDatabaseHas('organizations', ['id' => $organization->id]);

    $this->actingAs($this->owner)->patch("/organizations/{$organization->id}/toggle-active");
    expect($organization->fresh()->is_active)->toBeTrue();
});

test('deactivating a company hides it from a non-admin member immediately', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $manager = User::factory()->create();
    OrgMember::create([
        'organization_id' => $organization->id,
        'user_id' => $manager->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    expect($manager->visibleOrganizationIds())->toContain($organization->id);

    $this->actingAs($this->owner)->patch("/organizations/{$organization->id}/toggle-active");

    expect($manager->visibleOrganizationIds())->not->toContain($organization->id);
});

test('an owner still sees an inactive company in the admin list', function () {
    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75', 'is_active' => false]);

    $response = $this->actingAs($this->owner)->get('/organizations');

    $response->assertOk();
    $response->assertSee('Org A');
});
