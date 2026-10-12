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
 * flushing in between, and compared.
 *
 * WHO IS SIGNED IN MATTERS, and the matrix runs twice for it. Department
 * access went through AccessPermission and isClientOnProject() through
 * Project, both of which carry BelongsToOrganization, whose scope filters
 * rows to the SIGNED-IN user's visibleOrganizationIds() whenever that user
 * has no global role. So every case below is asked both with the subject
 * signed in as themselves and with a different, non-global user signed in
 * (a manager asking about an assignee) — the second configuration is the
 * one that catches a profile that answers from its own data and forgets
 * the viewer. The fixture includes a DEACTIVATED company in which users
 * still hold live department grants and client attachments, because
 * visibleOrganizationIds() excludes deactivated companies and that makes
 * the gap visible even when the viewer IS the subject.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    // Switched off by an owner, but nothing beneath it was touched: the
    // grants, memberships and client rows in here are all still live.
    $this->orgDead = Organization::create([
        'name' => 'Org Dead', 'slug' => 'org-dead', 'accent_color' => '#9A3412', 'is_active' => false,
    ]);

    $this->deptA1 = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#111111']);
    $this->deptA2 = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Sales', 'color' => '#222222']);
    $this->deptB1 = Department::create(['organization_id' => $this->orgB->id, 'name' => 'Ops', 'color' => '#333333']);
    $this->deptDead = Department::create(['organization_id' => $this->orgDead->id, 'name' => 'Training', 'color' => '#444444']);

    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);
    $this->projectB = Project::create(['organization_id' => $this->orgB->id, 'name' => 'Project B', 'description' => 'd']);
    $this->projectDead = Project::create(['organization_id' => $this->orgDead->id, 'name' => 'Project Dead', 'description' => 'd']);

    // The "someone else is asking" viewer: non-global, and a member of
    // orgA only, so orgB and orgDead are both outside what they can see.
    $this->otherViewer = User::factory()->create();
    addOrgRole($this->otherViewer, $this->orgA, 'management');
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

/**
 * Every helper, asked about every company/department/project in the
 * fixture — run once per viewer configuration.
 *
 * Signing in as someone OTHER than the subject is not an edge case here:
 * TaskPolicy, Task::scopeVisibleTo() and the Tasks list all ask these
 * questions about users who are not the one making the request (an
 * assignee, a project's client), and the global scopes made those answers
 * depend on the asker. Both configurations use a NON-GLOBAL viewer,
 * because a global-role viewer bypasses the scopes entirely and so can
 * never show the difference.
 */
function assertEveryHelperAgrees(object $context, User $user, string $label): void
{
    $viewers = [
        'viewer is the subject' => $user,
        'viewer is a different non-global user' => $context->otherViewer,
    ];

    foreach ($viewers as $viewerLabel => $viewer) {
        $context->actingAs($viewer);
        assertEveryHelperAgreesForCurrentViewer($context, $user, "{$label} [{$viewerLabel}]");
    }
}

function assertEveryHelperAgreesForCurrentViewer(object $context, User $user, string $label): void
{
    $orgs = [$context->orgA, $context->orgB, $context->orgDead];
    $depts = [$context->deptA1, $context->deptA2, $context->deptB1, $context->deptDead];
    $projects = [$context->projectA, $context->projectB, $context->projectDead];

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

    assertEveryHelperAgrees($this, $user, 'super admin');
});

test('owner: cached answers match uncached', function () {
    assertEveryHelperAgrees($this, $this->owner, 'owner');
});

test('management in one company: cached answers match uncached', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'management');

    assertEveryHelperAgrees($this, $user, 'management');
});

test('staff with no department grants: cached answers match uncached', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'staff');

    assertEveryHelperAgrees($this, $user, 'staff, 0 departments');
});

test('staff with one department grant: cached answers match uncached', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'staff');
    grantDept($user, $this->orgA, $this->deptA1);

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

    assertEveryHelperAgrees($this, $user, 'staff, 1 active + 1 deactivated department');
});

test('staff with an explicitly DENIED grant (allowed = false)', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'staff');
    grantDept($user, $this->orgA, $this->deptA1, allowed: true);
    grantDept($user, $this->orgA, $this->deptA2, allowed: false);

    assertEveryHelperAgrees($this, $user, 'staff, 1 allowed + 1 denied');
});

test('client on one project: cached answers match uncached', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'client');
    $this->projectA->clients()->attach($user->id);

    assertEveryHelperAgrees($this, $user, 'client');
});

test('staff holding LIVE department grants in a DEACTIVATED company', function () {
    // The gap this phase had to close. The grants are real and allowed,
    // and the departments are active — only the COMPANY is switched off.
    // Before the cache, AccessPermission's BelongsToOrganization scope
    // dropped these rows for any non-global viewer, because
    // visibleOrganizationIds() excludes a deactivated company. Reading
    // access_permissions with a raw query skipped that filter, so the
    // cached helpers started reporting access in a company nobody can
    // see. Both viewer configurations must still say no.
    $user = User::factory()->create();
    addOrgRole($user, $this->orgDead, 'staff');
    grantDept($user, $this->orgDead, $this->deptDead);
    // A live company too, so a blanket "deny everything" fix wouldn't pass.
    addOrgRole($user, $this->orgA, 'staff');
    grantDept($user, $this->orgA, $this->deptA1);

    assertEveryHelperAgrees($this, $user, 'staff granted in a deactivated company');
});

test('client attached to a project in a DEACTIVATED company', function () {
    // The same gap on the other side: isClientOnProject() went through
    // projectsAsClient(), and Project carries BelongsToOrganization too.
    $user = User::factory()->create();
    addOrgRole($user, $this->orgDead, 'client');
    $this->projectDead->clients()->attach($user->id);
    addOrgRole($user, $this->orgA, 'client');
    $this->projectA->clients()->attach($user->id);

    assertEveryHelperAgrees($this, $user, 'client on a project in a deactivated company');
});

test('an OWNER subject still sees a deactivated company, whoever is asking', function () {
    // The mirror of the two above: a global role bypasses the scope, so
    // the deactivated company must NOT be filtered out of the subject's
    // own answers — the filter follows the viewer, not the subject.
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('slug', 'owner')->firstOrFail()->id);
    grantDept($user, $this->orgDead, $this->deptDead);
    $this->projectDead->clients()->attach($user->id);

    assertEveryHelperAgrees($this, $user, 'owner granted in a deactivated company');
});

test('a user holding DIFFERENT roles in two companies', function () {
    $user = User::factory()->create();
    addOrgRole($user, $this->orgA, 'management');
    addOrgRole($user, $this->orgB, 'staff');
    grantDept($user, $this->orgB, $this->deptB1);

    assertEveryHelperAgrees($this, $user, 'management in A, staff in B');
});

test('a user with no roles at all', function () {
    $user = User::factory()->create();

    assertEveryHelperAgrees($this, $user, 'no roles');
});

test('an owner who ALSO holds a company role', function () {
    $user = User::factory()->create();
    $user->roles()->attach(Role::where('slug', 'owner')->firstOrFail()->id);
    addOrgRole($user, $this->orgA, 'staff');
    grantDept($user, $this->orgA, $this->deptA1);

    assertEveryHelperAgrees($this, $user, 'owner + staff in A');
});
