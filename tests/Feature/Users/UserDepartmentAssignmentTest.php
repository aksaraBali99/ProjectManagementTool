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

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b']);

    $this->deptA1 = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptA2 = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Sales', 'color' => '#000000']);
    $this->deptB1 = Department::create(['organization_id' => $this->orgB->id, 'name' => 'Operations', 'color' => '#000000']);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach($this->roles['owner']->id);
});

function sectionStyleFor(string $html, int $organizationId): string
{
    preg_match(
        '/class="department-org-section" data-org-id="'.$organizationId.'" style="([^"]*)"/',
        $html,
        $matches
    );

    return $matches[1] ?? '';
}

test('a non-owner/non-super-admin cannot reach the user edit page at all, department tab included', function () {
    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $manager->id, 'role_id' => $this->roles['management']->id]);
    $target = User::factory()->create();

    $this->actingAs($manager)->get('/users/create')->assertForbidden();
    $this->actingAs($manager)->get("/users/{$target->id}/edit")->assertForbidden();
});

test('the edit page pre-renders every organization\'s department checkboxes up front, not just the currently-staff ones', function () {
    // Client-side JS toggles visibility off Tab 2's live selects with no
    // server round trip — that only works if every org's checkboxes are
    // already in the page, regardless of that org's current role. Org B
    // isn't staff here at all, but its department should still be present.
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $response = $this->actingAs($this->owner)->get("/users/{$target->id}/edit");

    $response->assertOk();
    $response->assertSee('value="'.$this->deptA1->id.'"', false);
    $response->assertSee('value="'.$this->deptB1->id.'"', false);
});

test('a staff-role company\'s department section renders visible, a non-staff company\'s renders hidden', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $target->id, 'role_id' => $this->roles['management']->id]);

    $response = $this->actingAs($this->owner)->get("/users/{$target->id}/edit");
    $html = $response->getContent();

    expect(sectionStyleFor($html, $this->orgA->id))->not->toContain('display: none;')
        ->and(sectionStyleFor($html, $this->orgB->id))->toContain('display: none;');
});

test('each role-select carries a data-org-id matching its department section, so client JS can pair them without a server round trip', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    $response = $this->actingAs($this->owner)->get("/users/{$target->id}/edit");

    $response->assertOk();
    $response->assertSee('data-org-id="'.$this->orgA->id.'"', false);
    $response->assertSee('class="department-org-section" data-org-id="'.$this->orgA->id.'"', false);
});

test('saving a management-role company\'s submitted department checkboxes are ignored, even though the checkbox markup exists for every org', function () {
    // Simulates: admin had Org A as Staff with a department checked, then
    // switched Org A to Management in Tab 2 before saving — the checkbox
    // markup for Org A's department still exists in the DOM (hidden), and
    // a stale/tampered submission could still include it. The final
    // ROLE the admin submitted is what governs what's actually saved.
    $target = User::factory()->create();

    $response = $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'management'],
        'access_permissions' => [$this->deptA1->id],
    ]);

    $response->assertRedirect('/users');
    expect(AccessPermission::where('user_id', $target->id)->where('department_id', $this->deptA1->id)->exists())->toBeFalse();
});

test('switching a company from staff to management on save drops its previously granted department access', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);
    AccessPermission::create(['user_id' => $target->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->deptA1->id, 'allowed' => true]);

    $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'management'],
    ]);

    expect(AccessPermission::where('user_id', $target->id)->where('organization_id', $this->orgA->id)->exists())->toBeFalse()
        ->and($target->fresh()->isManagementInOrg($this->orgA->id))->toBeTrue();
});

test('saving grants department access only for companies submitted as staff, scoped to that one user', function () {
    $target = User::factory()->create();
    $otherStaff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $otherStaff->id, 'role_id' => $this->roles['staff']->id]);
    AccessPermission::create(['user_id' => $otherStaff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->deptA1->id, 'allowed' => true]);

    $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [
            $this->orgA->id => 'staff',
            $this->orgB->id => 'staff',
        ],
        'access_permissions' => [$this->deptA1->id, $this->deptA2->id, $this->deptB1->id],
    ]);

    $target->refresh();
    expect($target->hasDepartmentAccess($this->orgA->id, $this->deptA1->id))->toBeTrue()
        ->and($target->hasDepartmentAccess($this->orgA->id, $this->deptA2->id))->toBeTrue()
        ->and($target->hasDepartmentAccess($this->orgB->id, $this->deptB1->id))->toBeTrue();

    // The other user's own grant, made in a separate save, is untouched.
    expect($otherStaff->fresh()->hasDepartmentAccess($this->orgA->id, $this->deptA1->id))->toBeTrue();
});

test('a department belonging to a different company than the one it was submitted under is ignored', function () {
    $target = User::factory()->create();

    $this->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        // Org A is staff, but the submitted department belongs to Org B —
        // which the admin never granted this user any role in at all.
        'roles' => [$this->orgA->id => 'staff'],
        'access_permissions' => [$this->deptB1->id],
    ]);

    expect(AccessPermission::where('user_id', $target->id)->exists())->toBeFalse();
});

test('creating a user, its company roles, and its department access all commit together on save', function () {
    $response = $this->actingAs($this->owner)->post('/users', [
        'username' => 'newperson',
        'password' => 'Str0ng!Passw0rd',
        'name' => 'New Person',
        'employee_id' => 'EMP-9001',
        'email' => 'newperson@example.com',
        'phone' => '+1 202 555 0100',
        'roles' => [$this->orgA->id => 'staff'],
        'access_permissions' => [$this->deptA1->id],
    ]);

    $response->assertRedirect('/users');

    $user = User::where('username', 'newperson')->firstOrFail();
    expect($user->isStaffInOrg($this->orgA->id))->toBeTrue()
        ->and($user->hasDepartmentAccess($this->orgA->id, $this->deptA1->id))->toBeTrue();
});

test('a validation error on a Tab 1 field, submitted alongside valid Tab 2/3 data, redirects back to a page marked to open on Tab 1', function () {
    $target = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $target->id, 'role_id' => $this->roles['staff']->id]);

    // Establishes the edit page as the session's "previous URL", which is
    // what a redirect-back-on-validation-failure targets — without this,
    // followingRedirects() below would land somewhere else entirely.
    $this->actingAs($this->owner)->get("/users/{$target->id}/edit");

    $response = $this->followingRedirects()->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => '', // invalid: required
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'staff'],
        'access_permissions' => [$this->deptA1->id],
    ]);

    $response->assertOk();
    $response->assertSee('data-initial-tab="1"', false);
    $response->assertSee('The username field is required.');
});

test('a validation error on a Tab 3 field (an invalid department id) redirects back to a page marked to open on Tab 3', function () {
    $target = User::factory()->create();

    $this->actingAs($this->owner)->get("/users/{$target->id}/edit");

    $response = $this->followingRedirects()->actingAs($this->owner)->put("/users/{$target->id}", [
        'username' => $target->username,
        'name' => $target->name,
        'employee_id' => $target->employee_id,
        'email' => $target->email,
        'phone' => $target->phone,
        'roles' => [$this->orgA->id => 'staff'],
        'access_permissions' => [999999], // non-existent department id
    ]);

    $response->assertOk();
    $response->assertSee('data-initial-tab="3"', false);
});
