<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);

    $this->projectA = Project::create([
        'organization_id' => $this->orgA->id,
        'name' => 'Project A',
        'description' => 'd',
    ]);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);
});

function makeStaffForDocumentList(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    return $staff;
}

function makeClientForDocumentList(Organization $org, ?Project $project = null): User
{
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    if ($project) {
        $project->clients()->attach($client->id);
    }

    return $client;
}

function makeDocumentForList(Organization $org, User $uploader, string $level, string $name = 'Doc'): Document
{
    return Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => $name,
        'link' => 'https://example.com/'.strtolower(str_replace(' ', '-', $name)),
        'access_level' => $level,
    ]);
}

test('a staff user with view_documents sees the Documents tab and list, filtered to what they can view', function () {
    $staff = makeStaffForDocumentList($this->orgA);
    $ownPrivate = makeDocumentForList($this->orgA, $staff, 'private', 'Own private');
    $othersPrivate = makeDocumentForList($this->orgA, $this->management, 'private', 'Others private');
    $internal = makeDocumentForList($this->orgA, $this->management, 'internal', 'Internal doc');

    $response = $this->actingAs($staff)->get('/documents/'.$this->orgA->id);

    $response->assertOk();
    $response->assertSee('Org A');
    $response->assertSee('Own private');
    $response->assertSee('Internal doc');
    $response->assertDontSee('Others private');

    $listedIds = $response->viewData('documents')->pluck('id')->sort()->values()->all();
    expect($listedIds)->toBe(collect([$ownPrivate->id, $internal->id])->sort()->values()->all());
});

test('a user with no document access to any company sees the empty-state Documents page', function () {
    $orphan = User::factory()->create();

    $response = $this->actingAs($orphan)->get('/documents');

    $response->assertOk();
    $response->assertSee("You don't have access to any companies yet.", false);
});

test('a client with an attached project gets a Documents tab, listing only that project\'s public documents', function () {
    $client = makeClientForDocumentList($this->orgA, $this->projectA);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task on my project',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $publicLinked = makeDocumentForList($this->orgA, $this->management, 'public', 'Public linked');
    $task->documents()->attach($publicLinked->id);

    $publicUnlinked = makeDocumentForList($this->orgA, $this->management, 'public', 'Public unlinked');
    $internalLinked = makeDocumentForList($this->orgA, $this->management, 'internal', 'Internal linked');
    $task->documents()->attach($internalLinked->id);

    $response = $this->actingAs($client)->get('/documents');

    $response->assertOk();
    $response->assertSee('Org A');

    $listedIds = $response->viewData('documents')->pluck('id')->all();
    expect($listedIds)->toBe([$publicLinked->id]);
    $response->assertDontSee('Public unlinked');
    $response->assertDontSee('Internal linked');
});

test('canManage on the Documents list is true for management and false for staff', function () {
    $staff = makeStaffForDocumentList($this->orgA);

    $staffResponse = $this->actingAs($staff)->get('/documents/'.$this->orgA->id);
    $staffResponse->assertOk();
    expect($staffResponse->viewData('canManage'))->toBeFalse();

    $managementResponse = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);
    $managementResponse->assertOk();
    expect($managementResponse->viewData('canManage'))->toBeTrue();
});
