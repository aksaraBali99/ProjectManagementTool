<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Support\Access\AccessProfileCache;
use Illuminate\Support\Collection;

/**
 * task #72 phase 1: the cached helpers must give byte-identical answers
 * to the uncached ones, for every kind of user and every argument.
 *
 * This is the test that makes the whole phase safe: if a single answer
 * differs, someone somewhere can see or do something they could not
 * before, which is precisely what this phase promised not to change.
 *
 * Each case is asked twice — once with solava.access_cache on, once off —
 * flushing in between, and compared. The user being asked about is also
 * the authenticated user, because that is how every real caller works
 * (policies, scopeVisibleTo, the views) and because Department and
 * AccessPermission carry viewer-dependent global scopes, so the uncached
 * path genuinely depends on who is logged in.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $this->deptA1 = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#111111']);
    $this->deptA2 = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Sales', 'color' => '#222222']);
    $this->deptB1 = Department::create(['organization_id' => $this->orgB->id, 'name' => 'Ops', 'color' => '#333333']);

    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);
    $this->projectB = Project::create(['organization_id' => $this->orgB->id, 'name' => 'Project B', 'description' => 'd']);
});

function addOrgRole(User $user, Organization $org, string $slug): void
{
    OrgMember::updateOrCreate(
        ['organization_id' => $org->id, 'user_id' => $user->id],
        ['role_id' => Role::where('slug', $slug)->firstOrFail()->id],
    );
}

function grantDept(User $user, Organization $org, Department $dept, bool $allowed = true): void
{
    AccessPermission::updateOrCreate(
        ['user_id' => $user->id, 'organization_id' => $org->id, 'department_id' => $dept->id],
        ['allowed' => $allowed],
    );
}

/**
 * Runs $ask with the cache off and then on, flushing in between, and
 * returns both answers for comparison.
 *
 * @return array{off: mixed, on: mixed}
 */
function bothWays(User $user, callable $ask): array
{
    $normalise = fn ($value) => $value instanceof Collection
        ? $value->map(fn ($v) => (int) $v)->sort()->values()->all()
        : $value;

    config(['solava.access_cache' => false]);
    app(AccessProfileCache::class)->flush();
    $off = $normalise($ask($user->fresh()));

    config(['solava.access_cache' => true]);
    app(AccessProfileCache::class)->flush();
    $on = $normalise($ask($user->fresh()));

    return ['off' => $off, 'on' => $on];
}

/** Every helper, asked about every company/department/project in the fixture. */
function assertEveryHelperAgrees(object $context, User $user, string $label): void
{
    $orgs = [$context->orgA, $context->orgB];
    $depts = [$context->deptA1, $context->deptA2, $context->deptB1];
    $projects = [$context->projectA, $context->projectB];

    $checks = [
        'isSuperAdmin' => fn (User $u) => $u->isSuperAdmin(),
        'isOwner' => fn (User $u) => $u->isOwner(),
        'hasGlobalRole' => fn (User $u) => $u->hasGlobalRole(),
    ];

    foreach ($orgs as $org) {
        $checks["isManagementInOrg({$org->id})"] = fn (User $u) => $u->isManagementInOrg($org->id);
        $checks["isStaffInOrg({$org->id})"] = fn (User $u) => $u->isStaffInOrg($org->id);
        $checks["isClientInOrg({$org->id})"] = fn (User $u) => $u->isClientInOrg($org->id);
        $checks["allowedDepartmentIds({$org->id})"] = fn (User $u) => $u->allowedDepartmentIds($org->id);
        $checks["allowedActiveDepartmentIds({$org->id})"] = fn (User $u) => $u->allowedActiveDepartmentIds($org->id);

        foreach ($depts as $dept) {
            $checks["hasDepartmentAccess({$org->id},{$dept->id})"] = fn (User $u) => $u->hasDepartmentAccess($org->id, $dept->id);
        }

        // Every permission slug that exists, not a sample.
        foreach (Permission::pluck('slug') as $slug) {
            $checks["hasPermission({$slug},{$org->id})"] = fn (User $u) => $u->hasPermission($slug, $org->id);
        }
    }

    foreach (Permission::pluck('slug') as $slug) {
        $checks["hasPermission({$slug},null)"] = fn (User $u) => $u->hasPermission($slug);
    }

    foreach ($projects as $project) {
        $checks["isClientOnProject({$project->id})"] = fn (User $u) => $u->isClientOnProject($project->id);
    }

    // Org-list helpers, which call the above many times over.
    $checks['manageableOrganizationIds'] = fn (User $u) => $u->manageableOrganizationIds();
    $checks['visibleOrganizationIds'] = fn (User $u) => $u->visibleOrganizationIds();
    $checks['boardOrganizationIds(view_dashboard)'] = fn (User $u) => $u->boardOrganizationIds('view_dashboard');
    $checks['boardOrganizationIds(view_kanban)'] = fn (User $u) => $u->boardOrganizationIds('view_kanban');
    $checks['documentOrganizationIds'] = fn (User $u) => $u->documentOrganizationIds();
    $checks['canAccessDocumentsPage'] = fn (User $u) => $u->canAccessDocumentsPage();
    $checks['documentManageableOrganizationIds'] = fn (User $u) => $u->documentManageableOrganizationIds();

    foreach ($checks as $name => $ask) {
        $answers = bothWays($user, $ask);
        expect($answers['on'])->toBe(
            $answers['off'],
            "{$label}: {$name} differs — cached said ".json_encode($answers['on'])
            .', uncached said '.json_encode($answers['off'])
        );
    }
}

test('super admin: cached answers match uncached', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id);

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'super admin');
});

test('owner: cached answers match uncached', function () {
    $this->actingAs($this->owner);
    assertEveryHelperAgrees($this, $this->owner, 'owner');
});

test('management in one company: cached answers match uncached', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'management');

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'management');
});

test('staff with no department grants: cached answers match uncached', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'staff');

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'staff, 0 departments');
});

test('staff with one department grant: cached answers match uncached', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'staff');
    grantDept($user, $this->orgA, $this->deptA1);

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'staff, 1 department');
});

test('staff with two grants, one of them a DEACTIVATED department', function () {
    // The case allowedDepartmentIds() and allowedActiveDepartmentIds()
    // disagree on — the single most likely place for the cache to differ,
    // since both come from one load.
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'staff');
    grantDept($user, $this->orgA, $this->deptA1);
    grantDept($user, $this->orgA, $this->deptA2);
    $this->deptA2->update(['is_active' => false]);

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'staff, 1 active + 1 deactivated department');
});

test('staff with an explicitly DENIED grant (allowed = false)', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'staff');
    grantDept($user, $this->orgA, $this->deptA1, allowed: true);
    grantDept($user, $this->orgA, $this->deptA2, allowed: false);

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'staff, 1 allowed + 1 denied');
});

test('client on one project: cached answers match uncached', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'client');
    $this->projectA->clients()->attach($user->id);

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'client');
});

test('a user holding DIFFERENT roles in two companies', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'management');
    addOrgRole($user, $this->orgB, 'staff');
    grantDept($user, $this->orgB, $this->deptB1);

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'management in A, staff in B');
});

test('a user with no roles at all', function () {
    $user = User::factory()->create();

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'no roles');
});

test('an owner who ALSO holds a company role', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('slug', 'owner')->firstOrFail()->id);
    addOrgRole($user, $this->orgA, 'staff');
    grantDept($user, $this->orgA, $this->deptA1);

    $this->actingAs($user);
    assertEveryHelperAgrees($this, $user, 'owner + staff in A');
});
