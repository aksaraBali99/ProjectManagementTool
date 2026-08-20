<?php

use App\Enums\Priority;
use App\Enums\ProjectStatus;
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
    // Real seeders (not hand-rolled Role::create rows) so these roles carry
    // the role_permissions grants ProjectPolicy now checks via
    // hasPermission() — a locally-created Role with no seeded permissions
    // fails every capability check.
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->roles = collect(['super_admin', 'owner', 'management', 'staff', 'client'])
        ->mapWithKeys(fn ($slug) => [$slug => Role::where('slug', $slug)->firstOrFail()]);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#534AB7']);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach($this->roles['owner']->id);

    $this->manager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->manager->id, 'role_id' => $this->roles['management']->id]);

    $this->staffInA = User::factory()->create(['name' => 'Staff In A']);
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->staffInA->id, 'role_id' => $this->roles['staff']->id]);

    $this->staffInB = User::factory()->create(['name' => 'Staff In B']);
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $this->staffInB->id, 'role_id' => $this->roles['staff']->id]);
});

function validProjectPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Website revamp',
        'description' => 'Rebuild the marketing site.',
        'client' => 'Acme Corp',
        'status' => 'open',
        'priority' => 'medium',
    ], $overrides);
}

test('an owner can create a project end to end', function () {
    $response = $this->actingAs($this->owner)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgA->id,
        'staff' => [$this->staffInA->id],
    ]));

    $response->assertRedirect("/projects/{$this->orgA->id}");
    $this->assertDatabaseHas('projects', [
        'organization_id' => $this->orgA->id,
        'name' => 'Website revamp',
        'client_name' => 'Acme Corp',
        'is_external' => true,
        'status' => 'open',
        'priority' => 'medium',
    ]);

    $project = Project::where('name', 'Website revamp')->firstOrFail();
    expect($project->staff->pluck('id')->all())->toBe([$this->staffInA->id]);
});

test('a project can be created and edited with an unselected (blank) staff row', function () {
    $response = $this->actingAs($this->owner)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgA->id,
        'staff' => [''],
    ]));

    $response->assertRedirect("/projects/{$this->orgA->id}");
    $project = Project::where('name', 'Website revamp')->firstOrFail();
    expect($project->staff)->toBeEmpty();

    $response = $this->actingAs($this->owner)->put("/projects/{$project->id}", validProjectPayload([
        'staff' => ['', $this->staffInA->id, ''],
    ]));

    $response->assertRedirect("/projects/{$this->orgA->id}");
    expect($project->fresh()->staff->pluck('id')->all())->toBe([$this->staffInA->id]);
});

test('entering "internal" as the client marks the project internal, case-insensitively', function () {
    $this->actingAs($this->owner)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgA->id,
        'client' => 'Internal',
    ]));

    $this->assertDatabaseHas('projects', [
        'name' => 'Website revamp',
        'client_name' => 'Internal',
        'is_external' => false,
    ]);
});

test('a project can be edited and persists the assigned staff and field changes', function () {
    $project = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Old name',
        'description' => 'Old description',
        'client_name' => 'Old Client',
        'status' => 'open',
        'priority' => 'low',
    ]);

    $response = $this->actingAs($this->owner)->put("/projects/{$project->id}", validProjectPayload([
        'name' => 'New name',
        'status' => 'closed',
        'priority' => 'high',
        'staff' => [$this->staffInA->id],
    ]));

    $response->assertRedirect("/projects/{$this->orgA->id}");
    $project->refresh();
    expect($project->name)->toBe('New name')
        ->and($project->status)->toBe(ProjectStatus::Closed)
        ->and($project->priority)->toBe(Priority::High)
        ->and($project->staff->pluck('id')->all())->toBe([$this->staffInA->id]);
});

test('name, description, and client are mandatory', function () {
    $response = $this->actingAs($this->owner)->post('/projects', [
        'organization_id' => $this->orgA->id,
        'status' => 'open',
        'priority' => 'medium',
    ]);

    $response->assertSessionHasErrors(['name', 'description', 'client']);
    $this->assertDatabaseMissing('projects', ['organization_id' => $this->orgA->id]);
});

test('the assigned staff dropdown on the create form only offers members of the selected company', function () {
    $response = $this->actingAs($this->owner)->get("/projects/create/{$this->orgA->id}");

    $response->assertOk();
    $members = $response->viewData('organizationMembers');

    expect(collect($members[$this->orgA->id])->pluck('id')->all())->toContain($this->staffInA->id)
        ->and(collect($members[$this->orgA->id])->pluck('id')->all())->not->toContain($this->staffInB->id);
});

test('assigning a staff member who does not belong to the project company is rejected', function () {
    $response = $this->actingAs($this->owner)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgA->id,
        'staff' => [$this->staffInB->id],
    ]));

    $response->assertSessionHasErrors('staff.0');
    $this->assertDatabaseMissing('projects', ['organization_id' => $this->orgA->id, 'name' => 'Website revamp']);
});

test('a management user can create a project in their own company', function () {
    $response = $this->actingAs($this->manager)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgA->id,
    ]));

    $response->assertRedirect("/projects/{$this->orgA->id}");
    $this->assertDatabaseHas('projects', ['organization_id' => $this->orgA->id, 'name' => 'Website revamp']);
});

test('a management user cannot create a project in a company they do not manage', function () {
    $response = $this->actingAs($this->manager)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgB->id,
    ]));

    $response->assertForbidden();
});

test('a management user cannot view the create form for a company they do not manage', function () {
    $response = $this->actingAs($this->manager)->get("/projects/create/{$this->orgB->id}");

    // Falls back to a company they do manage rather than exposing Org B's form.
    $response->assertOk();
    $response->assertViewHas('organization', fn ($organization) => $organization->id === $this->orgA->id);
});

test('a management user can edit a project in their own company but not another company\'s project', function () {
    $projectA = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Project A',
        'description' => 'd',
        'client_name' => 'c',
    ]);
    $projectB = Project::create([
        'organization_id' => $this->orgB->id,
        'name' => 'Project B',
        'description' => 'd',
        'client_name' => 'c',
    ]);

    $this->actingAs($this->manager)->get("/projects/{$projectA->id}/edit")->assertOk();

    // Org-scoped global scope means a project outside the manager's visible orgs 404s on route binding.
    $this->actingAs($this->manager)->get("/projects/{$projectB->id}/edit")->assertNotFound();
    $this->actingAs($this->manager)->put("/projects/{$projectB->id}", validProjectPayload())->assertNotFound();
});

test('the project index only shows tabs for companies the user is visible in', function () {
    $response = $this->actingAs($this->manager)->get('/projects');

    $response->assertOk();
    $organizations = $response->viewData('organizations');

    expect($organizations->pluck('id')->all())->toBe([$this->orgA->id]);
});

test('the project index scopes the project list to the selected company tab', function () {
    Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd', 'client_name' => 'c']);
    Project::create(['organization_id' => $this->orgB->id, 'name' => 'Project B', 'description' => 'd', 'client_name' => 'c']);

    $response = $this->actingAs($this->owner)->get("/projects/{$this->orgA->id}");

    $response->assertOk()->assertSee('Project A')->assertDontSee('Project B');
});

test('a client user only sees the projects they are attached to, even within a visible company', function () {
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $client->id, 'role_id' => $this->roles['client']->id]);

    $theirProject = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Their Project', 'description' => 'd', 'client_name' => 'c']);
    $otherProject = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Other Project', 'description' => 'd', 'client_name' => 'c']);
    $theirProject->clients()->attach($client->id);

    $response = $this->actingAs($client)->get("/projects/{$this->orgA->id}");

    $response->assertOk()->assertSee('Their Project')->assertDontSee('Other Project');
});

test('the project index shows the task count per project', function () {
    $project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd', 'client_name' => 'c']);
    $department = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    Task::create(['organization_id' => $this->orgA->id, 'project_id' => $project->id, 'department_id' => $department->id, 'title' => 'A task']);

    $response = $this->actingAs($this->owner)->get("/projects/{$this->orgA->id}");

    $projects = $response->viewData('projects');
    expect($projects->firstWhere('id', $project->id)->tasks_count)->toBe(1);
});

test('templating a project pre-fills name, description, and priority', function () {
    $project = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Source Project',
        'description' => 'Source description',
        'client_name' => 'Acme',
        'status' => 'closed',
        'priority' => 'high',
    ]);

    $response = $this->actingAs($this->owner)->get("/projects/{$project->id}/template");

    $response->assertOk();
    expect($response->viewData('templateName'))->toBe('Source Project (Copy)')
        ->and($response->viewData('templateDescription'))->toBe('Source description')
        ->and($response->viewData('templatePriority'))->toBe('high');

    // Defaults to a different company than the source when one is available.
    expect($response->viewData('organization')->id)->not->toBe($this->orgA->id);
});

test('submitting a templated project copies name, description, and priority but not status, client, or staff', function () {
    $source = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Source Project',
        'description' => 'Source description',
        'client_name' => 'Acme',
        'status' => 'closed',
        'priority' => 'high',
    ]);
    $source->staff()->attach($this->staffInA->id);

    $response = $this->actingAs($this->owner)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgB->id,
        'name' => 'Source Project (Copy)',
        'description' => 'Source description',
        'priority' => 'high',
        'client' => 'Fresh Client For Org B',
    ]));

    $response->assertRedirect("/projects/{$this->orgB->id}");

    $copy = Project::where('organization_id', $this->orgB->id)->where('name', 'Source Project (Copy)')->firstOrFail();
    expect($copy->description)->toBe('Source description')
        ->and($copy->priority)->toBe(Priority::High)
        ->and($copy->status)->toBe(ProjectStatus::Open) // Always starts open, even though the source was closed.
        ->and($copy->client_name)->toBe('Fresh Client For Org B')
        ->and($copy->staff)->toBeEmpty(); // Staff is never carried over.
});

test('a super admin can template a project into any company', function () {
    $project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'P', 'description' => 'd', 'client_name' => 'c']);

    $response = $this->actingAs($this->owner)->get("/projects/{$project->id}/template");

    $response->assertOk();
    expect($response->viewData('organizations')->pluck('id')->all())->toBe([$this->orgA->id, $this->orgB->id]);
});

test('a management user cannot template a project into a company they do not manage', function () {
    $project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'P', 'description' => 'd', 'client_name' => 'c']);

    $response = $this->actingAs($this->manager)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgB->id,
        'name' => 'P (Copy)',
    ]));

    $response->assertForbidden();
});

test('a management user cannot template a project they have no visibility into at all', function () {
    $projectB = Project::create(['organization_id' => $this->orgB->id, 'name' => 'P', 'description' => 'd', 'client_name' => 'c']);

    $this->actingAs($this->manager)->get("/projects/{$projectB->id}/template")->assertNotFound();
});

test('a management user can template a project from a company they only have staff access to into a company they manage', function () {
    OrgMember::create(['organization_id' => $this->orgB->id, 'user_id' => $this->manager->id, 'role_id' => $this->roles['staff']->id]);
    $sourceInB = Project::create([
        'organization_id' => $this->orgB->id,
        'name' => 'Org B Project',
        'description' => 'd',
        'client_name' => 'c',
        'priority' => 'low',
    ]);

    $response = $this->actingAs($this->manager)->get("/projects/{$sourceInB->id}/template");

    $response->assertOk();
    expect($response->viewData('organization')->id)->toBe($this->orgA->id)
        ->and($response->viewData('templateName'))->toBe('Org B Project (Copy)');

    $submit = $this->actingAs($this->manager)->post('/projects', validProjectPayload([
        'organization_id' => $this->orgA->id,
        'name' => 'Org B Project (Copy)',
        'priority' => 'low',
    ]));

    $submit->assertRedirect("/projects/{$this->orgA->id}");
    $this->assertDatabaseHas('projects', ['organization_id' => $this->orgA->id, 'name' => 'Org B Project (Copy)']);
});
