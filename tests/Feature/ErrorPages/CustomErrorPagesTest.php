<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

test('visiting an unknown URL shows the friendly 404 page, not Laravel\'s default', function () {
    $response = $this->get('/this-page-does-not-exist');

    $response->assertNotFound();
    $response->assertSee('We couldn\'t find the page you\'re looking for.', false);
});

test('visiting a page without permission shows the friendly 403 page, not Laravel\'s default', function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $organization = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $organization->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    $response = $this->actingAs($staff)->get('/audit-trail');

    $response->assertForbidden();
    $response->assertSee('You don\'t have permission to view this page.', false);
});

test('the custom 500 page renders friendly copy', function () {
    $this->view('errors.500')->assertSee('Something went wrong on our end.');
});
