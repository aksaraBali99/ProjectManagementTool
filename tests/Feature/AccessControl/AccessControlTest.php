<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->roles = collect(['super_admin', 'owner', 'management', 'staff', 'client'])
        ->mapWithKeys(fn ($slug) => [$slug => Role::create(['name' => ucfirst($slug), 'slug' => $slug, 'is_system' => true])]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach($this->roles['owner']->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#EEEDFE']);
    $this->deptAInactive = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Old Dept', 'color' => '#000000', 'is_active' => false]);
});

test('a non-owner/non-super-admin cannot access the access control screen', function () {
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $manager->id, 'role_id' => $this->roles['management']->id]);

    $this->actingAs($manager)->get('/access-control')->assertForbidden();
    $this->actingAs($manager)->post('/access-control/toggle', [
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'user_id' => $manager->id,
        'allowed' => true,
    ])->assertForbidden();
});

test('the matrix only shows active companies as tabs', function () {
    $inactiveOrg = Organization::create(['name' => 'Inactive Co', 'slug' => 'inactive-co', 'accent_color' => '#000000', 'is_active' => false]);

    $response = $this->actingAs($this->owner)->get('/access-control');

    $response->assertOk();
    $response->assertSee('Org A');
    $response->assertSee('Org B');
    $response->assertDontSee('Inactive Co');
});

test('the matrix only shows active departments as columns', function () {
    $response = $this->actingAs($this->owner)->get("/access-control/{$this->orgA->id}");

    $response->assertOk();
    $response->assertSee('Marketing');
    $response->assertDontSee('Old Dept');
});

test('the matrix shows all active staff in the system, even those not yet assigned to the viewed company', function () {
    $staffElsewhere = User::factory()->create(['name' => 'Staff Elsewhere']);
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $staffElsewhere->id, 'role_id' => $this->roles['staff']->id]);

    $response = $this->actingAs($this->owner)->get("/access-control/{$this->orgA->id}");

    $response->assertOk();
    $response->assertSee('Staff Elsewhere');
});

test('an inactive user does not appear as a row', function () {
    $inactiveStaff = User::factory()->inactive()->create(['name' => 'Gone Staff']);
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $inactiveStaff->id, 'role_id' => $this->roles['staff']->id]);

    $response = $this->actingAs($this->owner)->get("/access-control/{$this->orgA->id}");

    $response->assertOk();
    $response->assertDontSee('Gone Staff');
});

test('toggling a permission on for a user with no org_members row in that company creates one with role staff', function () {
    $staff = User::factory()->create();

    expect(OrgMember::where('organization_id', $this->orgA->id)->where('user_id', $staff->id)->exists())->toBeFalse();

    $response = $this->actingAs($this->owner)->post('/access-control/toggle', [
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'user_id' => $staff->id,
        'allowed' => true,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('access_permissions', [
        'user_id' => $staff->id,
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'allowed' => true,
    ]);
    $membership = OrgMember::where('organization_id', $this->orgA->id)->where('user_id', $staff->id)->first();
    expect($membership)->not->toBeNull()
        ->and($membership->role_id)->toBe($this->roles['staff']->id);
});

test('toggling a permission for a user who already has management in that company does not change their role', function () {
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $manager->id, 'role_id' => $this->roles['management']->id]);

    $this->actingAs($this->owner)->post('/access-control/toggle', [
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'user_id' => $manager->id,
        'allowed' => true,
    ]);

    $membership = OrgMember::where('organization_id', $this->orgA->id)->where('user_id', $manager->id)->first();
    expect($membership->role_id)->toBe($this->roles['management']->id);
});

test('toggling a permission off does not remove the org_members row', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $this->roles['staff']->id]);
    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'allowed' => true,
    ]);

    $this->actingAs($this->owner)->post('/access-control/toggle', [
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'user_id' => $staff->id,
        'allowed' => false,
    ]);

    $this->assertDatabaseHas('access_permissions', [
        'user_id' => $staff->id,
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'allowed' => false,
    ]);
    expect(OrgMember::where('organization_id', $this->orgA->id)->where('user_id', $staff->id)->exists())->toBeTrue();
});

test('deactivating a company or department leaves access_permissions and org_members rows completely intact', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $this->roles['staff']->id]);
    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'allowed' => true,
    ]);

    $this->actingAs($this->owner)->patch("/organizations/{$this->orgA->id}/toggle-active");
    $this->actingAs($this->owner)->patch("/departments/{$this->deptA->id}/toggle-active");

    $this->assertDatabaseHas('org_members', ['organization_id' => $this->orgA->id, 'user_id' => $staff->id]);
    $this->assertDatabaseHas('access_permissions', [
        'user_id' => $staff->id,
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'allowed' => true,
    ]);
});
