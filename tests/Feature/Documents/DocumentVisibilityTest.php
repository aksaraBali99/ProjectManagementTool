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

function makeStaffForDocuments(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    return $staff;
}

function makeClientForDocuments(Organization $org, ?Project $project = null): User
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

function makeDocument(Organization $org, User $uploader, string $level, string $name = 'Doc'): Document
{
    return Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => $name,
        'link' => 'https://example.com/'.strtolower(str_replace(' ', '-', $name)),
        'access_level' => $level,
    ]);
}

test('a staff user cannot see another user\'s private document, but can see their own private document and any internal document in their company', function () {
    $staff = makeStaffForDocuments($this->orgA);
    $ownPrivate = makeDocument($this->orgA, $staff, 'private', 'Own private');
    $othersPrivate = makeDocument($this->orgA, $this->management, 'private', 'Others private');
    $internal = makeDocument($this->orgA, $this->management, 'internal', 'Internal doc');

    expect($staff->can('view', $ownPrivate))->toBeTrue()
        ->and($staff->can('view', $othersPrivate))->toBeFalse()
        ->and($staff->can('view', $internal))->toBeTrue();
});

test('management can see a private document they didn\'t upload', function () {
    $staff = makeStaffForDocuments($this->orgA);
    $staffPrivate = makeDocument($this->orgA, $staff, 'private', 'Staff private');

    expect($this->management->can('view', $staffPrivate))->toBeTrue();
});

test('a client sees public documents linked to their project, but not private/internal ones, and not public documents from projects they aren\'t the client of', function () {
    $client = makeClientForDocuments($this->orgA, $this->projectA);

    $taskOnMyProject = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task on my project',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $otherProject = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Other project', 'description' => 'd']);
    $taskOnOtherProject = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $otherProject->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task on other project',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $publicLinkedToMyProject = makeDocument($this->orgA, $this->management, 'public', 'Public, my project');
    $taskOnMyProject->documents()->attach($publicLinkedToMyProject->id);

    $publicLinkedToOtherProject = makeDocument($this->orgA, $this->management, 'public', 'Public, other project');
    $taskOnOtherProject->documents()->attach($publicLinkedToOtherProject->id);

    $publicUnlinked = makeDocument($this->orgA, $this->management, 'public', 'Public, unlinked');

    $internalLinkedToMyProject = makeDocument($this->orgA, $this->management, 'internal', 'Internal, my project');
    $taskOnMyProject->documents()->attach($internalLinkedToMyProject->id);

    $privateLinkedToMyProject = makeDocument($this->orgA, $this->management, 'private', 'Private, my project');
    $taskOnMyProject->documents()->attach($privateLinkedToMyProject->id);

    expect($client->can('view', $publicLinkedToMyProject))->toBeTrue()
        ->and($client->can('view', $publicLinkedToOtherProject))->toBeFalse()
        ->and($client->can('view', $publicUnlinked))->toBeFalse()
        ->and($client->can('view', $internalLinkedToMyProject))->toBeFalse()
        ->and($client->can('view', $privateLinkedToMyProject))->toBeFalse();
});

test('manage_documents is required to create a document; a view-only (staff) user gets a 403 attempting to POST', function () {
    $staff = makeStaffForDocuments($this->orgA);

    $this->actingAs($staff)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'New doc',
        'link' => 'https://example.com/new-doc.pdf',
        'access_level' => 'internal',
    ])->assertForbidden();

    $this->assertDatabaseMissing('documents', ['name' => 'New doc']);

    $this->actingAs($this->management)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'New doc',
        'link' => 'https://example.com/new-doc.pdf',
        'access_level' => 'internal',
    ])->assertRedirect();

    $this->assertDatabaseHas('documents', ['name' => 'New doc']);
});

test('a client cannot create a document', function () {
    $client = makeClientForDocuments($this->orgA, $this->projectA);

    $this->actingAs($client)->post('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Client attempt',
        'link' => 'https://example.com/client-attempt.pdf',
        'access_level' => 'public',
    ])->assertForbidden();
});
