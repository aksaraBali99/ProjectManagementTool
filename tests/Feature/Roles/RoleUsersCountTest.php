<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

/**
 * Role::users() only reaches the global user_roles pivot (how Super
 * Admin/Owner hold their role) — Management/Staff/Client hold theirs
 * per-company via org_members instead, so the Roles page's old plain
 * withCount('users') always showed 0 for those three regardless of how
 * many people actually held them. Also confirms the fix counts distinct
 * PEOPLE, not org_member rows, so someone holding the same role in two
 * companies isn't double-counted.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);
});

/** The rendered "Users" column value for the row whose name cell contains $roleName. */
function roleUsersCountFromPage(string $pageHtml, string $roleName): int
{
    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$pageHtml);
    libxml_clear_errors();

    $xpath = new DOMXPath($document);
    foreach ($xpath->query('//tr') as $row) {
        $cells = $xpath->query('.//td', $row);
        if ($cells->length < 3) {
            continue;
        }
        if (! str_contains(trim($cells->item(0)->textContent), $roleName)) {
            continue;
        }

        preg_match('/\d+/', $cells->item(2)->textContent, $matches);

        return (int) ($matches[0] ?? -1);
    }

    throw new RuntimeException("No role row found for \"{$roleName}\".");
}

test('the Roles page shows the real number of users for Management/Staff/Client, not always zero', function () {
    $managementRole = Role::where('slug', 'management')->firstOrFail();
    $staffRole = Role::where('slug', 'staff')->firstOrFail();

    $manager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $manager->id, 'role_id' => $managementRole->id]);

    $staffInOrgA = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staffInOrgA->id, 'role_id' => $staffRole->id]);

    $staffInOrgB = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $staffInOrgB->id, 'role_id' => $staffRole->id]);

    $page = $this->actingAs($this->owner)->get('/roles')->assertOk()->getContent();

    expect(roleUsersCountFromPage($page, 'Management'))->toBe(1);
    expect(roleUsersCountFromPage($page, 'Staff'))->toBe(2);
});

test('a user holding the same role in two companies is counted once, not once per company', function () {
    $staffRole = Role::where('slug', 'staff')->firstOrFail();

    $staffInBoth = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staffInBoth->id, 'role_id' => $staffRole->id]);
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $staffInBoth->id, 'role_id' => $staffRole->id]);

    $page = $this->actingAs($this->owner)->get('/roles')->assertOk()->getContent();

    expect(roleUsersCountFromPage($page, 'Staff'))->toBe(1);
});

test('Super Admin/Owner counts still come from the global user_roles pivot, unaffected by the fix', function () {
    $page = $this->actingAs($this->owner)->get('/roles')->assertOk()->getContent();

    // Only $this->owner exists, holding Owner globally — Super Admin has nobody.
    expect(roleUsersCountFromPage($page, 'Owner'))->toBe(1);
    expect(roleUsersCountFromPage($page, 'Super Admin'))->toBe(0);
});

test('a role with nobody assigned shows 0, not a missing/errored row', function () {
    $page = $this->actingAs($this->owner)->get('/roles')->assertOk()->getContent();

    expect(roleUsersCountFromPage($page, 'Client'))->toBe(0);
});
