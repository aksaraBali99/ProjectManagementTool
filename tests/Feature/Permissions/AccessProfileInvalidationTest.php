<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Services\CompanyRoleSyncer;
use App\Support\Access\AccessProfileCache;

/**
 * task #72 phase 1: a cached answer that outlives the data behind it is a
 * privilege bug, so every real write path gets a test here.
 *
 * The pattern in each case is the same: ask the question (warming the
 * profile), change the data through the path a real request would use,
 * then ask again and require the new answer.
 *
 * Two of these paths deserve special mention, because they are the ones
 * model events DON'T cover and so rely on an explicit flush:
 *   - CompanyRoleSyncer's 'none' branch: OrgMember::where(...)->delete()
 *   - UserManagementController's department-access replace:
 *     AccessPermission::where('user_id', ...)->delete()
 * Both are query-builder mass deletes, which load no model and therefore
 * fire no deleted event.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->otherDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Sales', 'color' => '#111111']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->user = User::factory()->create();
});

test('attaching a global role is seen immediately', function () {
    expect($this->user->isSuperAdmin())->toBeFalse();

    $this->user->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id);
    AccessProfileCache::flushAll();

    expect($this->user->fresh()->isSuperAdmin())->toBeTrue();
});

test('the UserManagementController super-admin toggle is seen immediately', function () {
    $superAdminId = Role::where('slug', 'super_admin')->firstOrFail()->id;

    $this->actingAs($this->owner);
    expect($this->user->isSuperAdmin())->toBeFalse();

    // Exactly what the controller does (pivot write, no model event).
    $this->user->roles()->syncWithoutDetaching([$superAdminId]);
    AccessProfileCache::flushAll();
    expect($this->user->fresh()->isSuperAdmin())->toBeTrue();

    $this->user->roles()->detach($superAdminId);
    AccessProfileCache::flushAll();
    expect($this->user->fresh()->isSuperAdmin())->toBeFalse();
});

test('adding a company role through CompanyRoleSyncer is seen immediately', function () {
    expect($this->user->isManagementInOrg($this->org->id))->toBeFalse();

    app(CompanyRoleSyncer::class)->sync($this->user, [$this->org->id => 'management']);

    expect($this->user->fresh()->isManagementInOrg($this->org->id))->toBeTrue();
});

test('REMOVING a company role through CompanyRoleSyncer is seen immediately', function () {
    // The mass-delete path: OrgMember::where(...)->delete() fires no
    // model event, so without the explicit flush the revoked role would
    // stay live for the rest of the request.
    app(CompanyRoleSyncer::class)->sync($this->user, [$this->org->id => 'management']);
    expect($this->user->fresh()->isManagementInOrg($this->org->id))->toBeTrue();

    app(CompanyRoleSyncer::class)->sync($this->user, [$this->org->id => 'none']);

    expect($this->user->fresh()->isManagementInOrg($this->org->id))->toBeFalse();
    $this->assertDatabaseMissing('org_members', ['organization_id' => $this->org->id, 'user_id' => $this->user->id]);
});

test('granting department access via AccessControlController is seen immediately', function () {
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    expect($this->user->hasDepartmentAccess($this->org->id, $this->dept->id))->toBeFalse();

    $this->actingAs($this->owner)->post('/access-control/toggle', [
        'organization_id' => $this->org->id,
        'department_id' => $this->dept->id,
        'user_id' => $this->user->id,
        'allowed' => true,
    ]);

    expect($this->user->fresh()->hasDepartmentAccess($this->org->id, $this->dept->id))->toBeTrue();
});

test('AccessControlController creating the OrgMember row is seen immediately', function () {
    // firstOrCreate is a normal Eloquent write, so the model hook covers
    // it — asserted anyway, since it is a role change like any other.
    expect($this->user->isStaffInOrg($this->org->id))->toBeFalse();

    $this->actingAs($this->owner)->post('/access-control/toggle', [
        'organization_id' => $this->org->id,
        'department_id' => $this->dept->id,
        'user_id' => $this->user->id,
        'allowed' => true,
    ]);

    expect($this->user->fresh()->isStaffInOrg($this->org->id))->toBeTrue();
    $this->assertDatabaseHas('org_members', ['organization_id' => $this->org->id, 'user_id' => $this->user->id]);
});

test('revoking department access is seen immediately', function () {
    AccessPermission::create([
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'department_id' => $this->dept->id,
        'allowed' => true,
    ]);
    expect($this->user->hasDepartmentAccess($this->org->id, $this->dept->id))->toBeTrue();

    AccessPermission::where('user_id', $this->user->id)
        ->where('department_id', $this->dept->id)
        ->first()
        ->update(['allowed' => false]);

    expect($this->user->fresh()->hasDepartmentAccess($this->org->id, $this->dept->id))->toBeFalse();
});

test('replacing department access wholesale (the mass delete) is seen immediately', function () {
    // UserManagementController::syncDepartmentAccess() deletes every row
    // for the user and re-creates them. The delete is a query-builder
    // mass delete and fires no model event.
    AccessPermission::create([
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'department_id' => $this->dept->id,
        'allowed' => true,
    ]);
    expect($this->user->hasDepartmentAccess($this->org->id, $this->dept->id))->toBeTrue();

    AccessPermission::where('user_id', $this->user->id)->delete();
    AccessProfileCache::flushAll();

    expect($this->user->fresh()->hasDepartmentAccess($this->org->id, $this->dept->id))->toBeFalse();
    expect($this->user->fresh()->allowedDepartmentIds($this->org->id)->all())->toBe([]);
});

test('deactivating a department is seen immediately', function () {
    AccessPermission::create([
        'user_id' => $this->user->id,
        'organization_id' => $this->org->id,
        'department_id' => $this->dept->id,
        'allowed' => true,
    ]);
    expect($this->user->hasDepartmentAccess($this->org->id, $this->dept->id))->toBeTrue();
    expect($this->user->allowedActiveDepartmentIds($this->org->id)->all())->toBe([$this->dept->id]);

    $this->dept->update(['is_active' => false]);

    $fresh = $this->user->fresh();
    expect($fresh->hasDepartmentAccess($this->org->id, $this->dept->id))->toBeFalse();
    expect($fresh->allowedActiveDepartmentIds($this->org->id)->all())->toBe([]);
    // The grant itself is untouched, so this one still reports it.
    expect($fresh->allowedDepartmentIds($this->org->id)->all())->toBe([$this->dept->id]);
});

test('changing a role s permissions through PermissionManagementController is seen immediately', function () {
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    expect($this->user->hasPermission('view_tasks', $this->org->id))->toBeTrue();

    // The real screen posts every role's full permission set.
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $keep = $staffRole->permissions()->where('slug', '!=', 'view_tasks')->pluck('permissions.id')->all();

    $this->actingAs($this->owner)->put('/roles/permissions', [
        'role_permissions' => [$staffRole->id => $keep],
    ]);

    expect($this->user->fresh()->hasPermission('view_tasks', $this->org->id))->toBeFalse();
});

test('adding and removing a project client is seen immediately', function () {
    expect($this->user->isClientOnProject($this->project->id))->toBeFalse();

    $this->project->clients()->attach($this->user->id);
    AccessProfileCache::flushAll();
    expect($this->user->fresh()->isClientOnProject($this->project->id))->toBeTrue();

    $this->project->clients()->detach($this->user->id);
    AccessProfileCache::flushAll();
    expect($this->user->fresh()->isClientOnProject($this->project->id))->toBeFalse();
});

test('the project form s client sync is seen immediately', function () {
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->firstOrFail()->id,
    ]);

    expect($client->isClientOnProject($this->project->id))->toBeFalse();

    $this->actingAs($this->owner)->put("/projects/{$this->project->id}", [
        'organization_id' => $this->org->id,
        'name' => $this->project->name,
        'description' => 'd',
        'status' => 'open',
        'priority' => 'medium',
        'client' => $client->id,
    ]);

    expect($client->fresh()->isClientOnProject($this->project->id))->toBeTrue();
});

test('two consecutive requests see a role change made between them', function () {
    // The request-isolation guarantee: one application instance serves
    // both, so without the flushing middleware request 2 could answer
    // from request 1's profile.
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->user->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    $this->actingAs($this->user)->get('/dashboard')->assertOk();
    expect($this->user->fresh()->isManagementInOrg($this->org->id))->toBeFalse();

    // Changed directly in the database: no model event, no flush call.
    DB::table('org_members')
        ->where('organization_id', $this->org->id)
        ->where('user_id', $this->user->id)
        ->update(['role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $this->actingAs($this->user)->get('/dashboard')->assertOk();
    expect($this->user->fresh()->isManagementInOrg($this->org->id))->toBeTrue();
});
