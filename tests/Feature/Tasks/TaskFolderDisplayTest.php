<?php

use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * Covers task #73 phase 4's display of a linked folder on the task page:
 * the initial page load (which folders are even shown, filtered entirely
 * for a Client) and the expand endpoint (GET /tasks/{task}/folders/
 * {folder}/expand — a folder's DIRECT children, filtered per viewer).
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->deptMarketing = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->deptSales = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Sales', 'color' => '#111111']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    // Marketing-only staff and Sales-only staff — for the "two different
    // viewers see different contents" test, both need manage_documents
    // (view_documents ties visibility here, not manage_documents, but
    // task view itself needs a department grant).
    $viewDocumentsId = Permission::where('slug', 'view_documents')->firstOrFail()->id;
    Role::where('slug', 'staff')->firstOrFail()->permissions()->syncWithoutDetaching([$viewDocumentsId]);
});

function makeDisplayTestFolder(Organization $org, User $creator, string $name = 'Folder'): DocumentFolder
{
    return DocumentFolder::create(['organization_id' => $org->id, 'parent_id' => null, 'name' => $name, 'created_by' => $creator->id]);
}

function makeDeptStaffForDisplayTest(Organization $org, Department $dept): User
{
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $org->id, 'department_id' => $dept->id, 'allowed' => true]);

    return $staff;
}

test('a linked folder renders as its own row on the task page, distinct from directly-attached files', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->deptMarketing->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $folder = makeDisplayTestFolder($this->orgA, $this->management, 'Launch assets');
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertSee('Launch assets');
    expect($response->getContent())->toContain('folder-row');
    expect($response->getContent())->toContain('data-folder-id="'.$folder->id.'"');
});

test('expanding a folder returns only its direct children, filtered per viewer - two staff viewing the same task see different contents', function () {
    // Both need Marketing department access to view THIS task at all
    // (TaskPolicy::view() is gated per the task's own department, not
    // just "some department in the org") — the axis that actually varies
    // between them is document-level visibility (Private is uploader-
    // only for a non-management viewer), not department access to the
    // task itself.
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->deptMarketing->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $folder = makeDisplayTestFolder($this->orgA, $this->management, 'Shared folder');
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $uploaderStaff = makeDeptStaffForDisplayTest($this->orgA, $this->deptMarketing);
    $otherStaff = makeDeptStaffForDisplayTest($this->orgA, $this->deptMarketing);

    $sharedDoc = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id,
        'name' => 'Shared.pdf', 'link' => 'https://example.com/shared.pdf', 'access_level' => 'internal', 'folder_id' => $folder->id,
    ]);
    $privateToUploader = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $uploaderStaff->id,
        'name' => 'Uploader-only.pdf', 'link' => 'https://example.com/uploader-only.pdf', 'access_level' => 'private', 'folder_id' => $folder->id,
    ]);

    $uploaderResponse = $this->actingAs($uploaderStaff)->getJson("/tasks/{$task->id}/folders/{$folder->id}/expand");
    $uploaderResponse->assertOk();
    $uploaderNames = collect($uploaderResponse->json('documents'))->pluck('name');
    expect($uploaderNames)->toContain('Shared.pdf', 'Uploader-only.pdf');

    $otherResponse = $this->actingAs($otherStaff)->getJson("/tasks/{$task->id}/folders/{$folder->id}/expand");
    $otherResponse->assertOk();
    $otherNames = collect($otherResponse->json('documents'))->pluck('name');
    expect($otherNames)->toContain('Shared.pdf');
    expect($otherNames)->not->toContain('Uploader-only.pdf');
});

test('expand also returns direct subfolders, unfiltered - folders have no visibility of their own', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->deptMarketing->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $folder = makeDisplayTestFolder($this->orgA, $this->management, 'Parent');
    $subfolder = makeDisplayTestFolder($this->orgA, $this->management, 'Child');
    $subfolder->update(['parent_id' => $folder->id]);
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $staff = makeDeptStaffForDisplayTest($this->orgA, $this->deptMarketing);

    $response = $this->actingAs($staff)->getJson("/tasks/{$task->id}/folders/{$folder->id}/expand");

    $response->assertOk();
    expect(collect($response->json('folders'))->pluck('id'))->toContain($subfolder->id);
});

test('a subfolder can itself be expanded, without needing to be independently linked to the task', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->deptMarketing->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $folder = makeDisplayTestFolder($this->orgA, $this->management, 'Parent');
    $subfolder = makeDisplayTestFolder($this->orgA, $this->management, 'Child');
    $subfolder->update(['parent_id' => $folder->id]);
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);
    $document = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id,
        'name' => 'Deep file.pdf', 'link' => 'https://example.com/deep-file.pdf', 'access_level' => 'internal', 'folder_id' => $subfolder->id,
    ]);

    $staff = makeDeptStaffForDisplayTest($this->orgA, $this->deptMarketing);

    // The subfolder itself is never in task_folder_links - only its
    // ancestor is - so this proves expand() doesn't gate on that row
    // existing, only on company match + task view + not-Client.
    $response = $this->actingAs($staff)->getJson("/tasks/{$task->id}/folders/{$subfolder->id}/expand");

    $response->assertOk();
    expect(collect($response->json('documents'))->pluck('name'))->toContain('Deep file.pdf');
});

test('a Client-role user sees no folder row at all for a task with a linked folder', function () {
    $client = User::factory()->create();
    $clientRole = Role::where('slug', 'client')->firstOrFail();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $client->id, 'role_id' => $clientRole->id]);
    $this->project->clients()->attach($client->id);

    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->deptMarketing->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $folder = makeDisplayTestFolder($this->orgA, $this->management, 'Hidden from client');
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $response = $this->actingAs($client)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $response->assertDontSee('Hidden from client');
    expect($response->getContent())->not->toContain('data-folder-id="'.$folder->id.'"');
});

test('a Client-role user is 404d on the expand endpoint directly, not just hidden client-side', function () {
    $client = User::factory()->create();
    $clientRole = Role::where('slug', 'client')->firstOrFail();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $client->id, 'role_id' => $clientRole->id]);
    $this->project->clients()->attach($client->id);

    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->deptMarketing->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $folder = makeDisplayTestFolder($this->orgA, $this->management, 'Folder');
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $response = $this->actingAs($client)->getJson("/tasks/{$task->id}/folders/{$folder->id}/expand");

    $response->assertNotFound();
});

test('an empty-for-this-viewer folder renders an empty result, not a hidden row or a count of what was excluded', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->deptMarketing->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $folder = makeDisplayTestFolder($this->orgA, $this->management, 'Folder');
    $task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    // A private document the viewer can't see, and nothing else - the
    // folder isn't empty in the database, but IS empty for this viewer.
    Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id,
        'name' => 'Hidden.pdf', 'link' => 'https://example.com/hidden.pdf', 'access_level' => 'private', 'folder_id' => $folder->id,
    ]);

    $staff = makeDeptStaffForDisplayTest($this->orgA, $this->deptMarketing);

    $response = $this->actingAs($staff)->getJson("/tasks/{$task->id}/folders/{$folder->id}/expand");

    $response->assertOk();
    expect($response->json('documents'))->toBe([]);
    expect($response->json('folders'))->toBe([]);
    // No count of hidden items anywhere in the response.
    expect($response->json())->not->toHaveKey('hidden_count');
});
