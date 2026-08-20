<?php

use App\Models\AccessPermission;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    // Uses the real seeders (not hand-rolled Role::create rows) so this
    // test's roles carry the same role_permissions grants TaskPolicy/etc.
    // now check via hasPermission() — a locally-created Role with no
    // seeded permissions would fail every capability check.
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->roles = collect(['super_admin', 'owner', 'management', 'staff', 'client'])
        ->mapWithKeys(fn ($slug) => [$slug => Role::where('slug', $slug)->firstOrFail()]);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b']);

    $this->deptAMarketing = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing']);
    $this->deptATechnology = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Technology']);
    $this->deptBMarketing = Department::create(['organization_id' => $this->orgB->id, 'name' => 'Marketing']);

    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd', 'client_name' => 'c']);
    $this->projectB = Project::create(['organization_id' => $this->orgB->id, 'name' => 'Project B', 'description' => 'd', 'client_name' => 'c']);

    $this->taskAMarketing = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->projectA->id, 'department_id' => $this->deptAMarketing->id, 'title' => 'A Marketing Task']);
    $this->taskATechnology = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->projectA->id, 'department_id' => $this->deptATechnology->id, 'title' => 'A Technology Task']);
    $this->taskB = Task::create(['organization_id' => $this->orgB->id, 'project_id' => $this->projectB->id, 'department_id' => $this->deptBMarketing->id, 'title' => 'B Task']);
});

function joinOrg(User $user, Organization $organization, Role $role): OrgMember
{
    return OrgMember::create([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
        'role_id' => $role->id,
    ]);
}

test('the organization scope blocks cross-tenant queries for a non-super-admin user', function () {
    $manager = User::factory()->create();
    joinOrg($manager, $this->orgA, $this->roles['management']);

    $this->actingAs($manager);

    expect(Task::pluck('title')->all())->toEqualCanonicalizing(['A Marketing Task', 'A Technology Task'])
        ->and(Project::pluck('name')->all())->toEqualCanonicalizing(['Project A']);
});

test('a staff user only sees tasks in departments they have access_permissions for', function () {
    $staff = User::factory()->create();
    joinOrg($staff, $this->orgA, $this->roles['staff']);

    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptAMarketing->id,
        'allowed' => true,
    ]);

    $this->actingAs($staff);

    expect($staff->can('view', $this->taskAMarketing))->toBeTrue()
        ->and($staff->can('view', $this->taskATechnology))->toBeFalse()
        ->and($staff->can('view', $this->taskB))->toBeFalse();
});

test('a management user sees all departments within their org but nothing outside it', function () {
    $manager = User::factory()->create();
    joinOrg($manager, $this->orgA, $this->roles['management']);

    $this->actingAs($manager);

    expect($manager->can('view', $this->taskAMarketing))->toBeTrue()
        ->and($manager->can('view', $this->taskATechnology))->toBeTrue()
        ->and($manager->can('view', $this->taskB))->toBeFalse();
});

test('a client user can view their own project but not others', function () {
    $client = User::factory()->create();
    joinOrg($client, $this->orgA, $this->roles['client']);
    $this->projectA->clients()->attach($client->id);

    $this->actingAs($client);

    expect($client->can('view', $this->projectA))->toBeTrue()
        ->and($client->can('view', $this->projectB))->toBeFalse();
});

test('a client can only edit their own comments, not others', function () {
    $client = User::factory()->create();
    joinOrg($client, $this->orgA, $this->roles['client']);
    $this->projectA->clients()->attach($client->id);

    $otherUser = User::factory()->create();

    $ownComment = Comment::create(['task_id' => $this->taskAMarketing->id, 'user_id' => $client->id, 'body' => 'mine']);
    $othersComment = Comment::create(['task_id' => $this->taskAMarketing->id, 'user_id' => $otherUser->id, 'body' => 'not mine']);

    $this->actingAs($client);

    expect($client->can('update', $ownComment))->toBeTrue()
        ->and($client->can('delete', $ownComment))->toBeTrue()
        ->and($client->can('update', $othersComment))->toBeFalse()
        ->and($client->can('delete', $othersComment))->toBeFalse();
});

test('super admin bypasses all organization scoping', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->roles()->attach($this->roles['super_admin']->id);

    $this->actingAs($superAdmin);

    expect(Task::pluck('title')->all())->toEqualCanonicalizing(['A Marketing Task', 'A Technology Task', 'B Task'])
        ->and($superAdmin->can('view', $this->taskB))->toBeTrue()
        ->and($superAdmin->can('update', $this->taskB))->toBeTrue()
        ->and($superAdmin->can('delete', $this->taskB))->toBeTrue();
});

test('owner bypasses all organization scoping', function () {
    $owner = User::factory()->create();
    $owner->roles()->attach($this->roles['owner']->id);

    $this->actingAs($owner);

    expect(Task::pluck('title')->all())->toEqualCanonicalizing(['A Marketing Task', 'A Technology Task', 'B Task'])
        ->and($owner->can('view', $this->projectB))->toBeTrue();
});
