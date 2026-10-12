<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use App\Support\Access\AccessProfileCache;

/**
 * task #72 phase 1: ACCESS_CACHE_ENABLED=false must restore the exact
 * previous behaviour — fresh queries every call, same answers.
 *
 * This is the rollback path, so it needs to be known-good rather than
 * assumed: if the cache ever causes trouble on dev or production, the
 * switch is the fix, and it has to work without a deploy.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);

    $this->staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->staff->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);
    AccessPermission::create([
        'user_id' => $this->staff->id,
        'organization_id' => $this->org->id,
        'department_id' => $this->dept->id,
        'allowed' => true,
    ]);
});

test('with the cache ON, repeated calls cost nothing after the first', function () {
    config(['solava.access_cache' => true]);
    app(AccessProfileCache::class)->flush();

    $first = countQueries(fn () => $this->staff->hasPermission('view_tasks', $this->org->id));
    $second = countQueries(fn () => $this->staff->hasPermission('view_tasks', $this->org->id));

    // Department grants are deferred, so the FIRST department question
    // costs exactly one query — the slice nothing had asked for yet — and
    // every one after it is free. Asserted separately rather than folded
    // into the loop below, so that one query stays visible instead of
    // being quietly absorbed into a looser bound.
    $firstDepartmentQuestion = countQueries(fn () => $this->staff->hasDepartmentAccess($this->org->id, $this->dept->id));

    $tenMore = countQueries(function () {
        for ($i = 0; $i < 10; $i++) {
            $this->staff->hasPermission('view_tasks', $this->org->id);
            $this->staff->isStaffInOrg($this->org->id);
            $this->staff->hasDepartmentAccess($this->org->id, $this->dept->id);
        }
    });

    expect($first)->toBeGreaterThan(0);        // the profile load
    expect($second)->toBe(0);                  // answered from memory
    expect($firstDepartmentQuestion)->toBe(1); // the one deferred slice
    expect($tenMore)->toBe(0);                 // 30 more questions, no queries
});

test('with the cache OFF, every call queries again', function () {
    config(['solava.access_cache' => false]);
    app(AccessProfileCache::class)->flush();

    $first = countQueries(fn () => $this->staff->hasPermission('view_tasks', $this->org->id));
    $second = countQueries(fn () => $this->staff->hasPermission('view_tasks', $this->org->id));

    expect($first)->toBeGreaterThan(0);
    expect($second)->toBe($first); // no memoisation at all — the old behaviour
});

test('with the cache OFF, the answers are unchanged', function () {
    $ask = fn (User $u) => [
        'perm' => $u->hasPermission('view_tasks', $this->org->id),
        'staff' => $u->isStaffInOrg($this->org->id),
        'mgmt' => $u->isManagementInOrg($this->org->id),
        'dept' => $u->hasDepartmentAccess($this->org->id, $this->dept->id),
        'allowed' => $u->allowedDepartmentIds($this->org->id)->all(),
        'active' => $u->allowedActiveDepartmentIds($this->org->id)->all(),
        'visible' => $u->visibleOrganizationIds(),
    ];

    config(['solava.access_cache' => true]);
    app(AccessProfileCache::class)->flush();
    $withCache = $ask($this->staff->fresh());

    config(['solava.access_cache' => false]);
    app(AccessProfileCache::class)->flush();
    $withoutCache = $ask($this->staff->fresh());

    expect($withCache)->toBe($withoutCache);
});

test('pages still render identically with the cache off', function () {
    config(['solava.access_cache' => false]);

    $this->actingAs($this->staff)->get(route('dashboard', $this->org, false))->assertOk();
    $this->actingAs($this->staff)->get(route('tasks.index', $this->org, false))->assertOk();
    $this->actingAs($this->staff)->get(route('kanban', $this->org, false))->assertOk();
});

test('the switch defaults to on', function () {
    // A missing ACCESS_CACHE_ENABLED must mean enabled, so an existing
    // .env without the key still gets the speed-up.
    expect(config('solava.access_cache'))->toBeTrue();
    expect(AccessProfileCache::enabled())->toBeTrue();
});
