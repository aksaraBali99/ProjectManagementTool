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
 * Covers task #73 phase 4's folder attach/detach endpoints
 * (POST /tasks/{task}/folders, DELETE /tasks/{task}/folders/{folder}) —
 * gated by TaskPolicy::attachDocuments()/unlinkDocuments(), reused
 * verbatim from Phase 3's file-attach/unlink, no folder-specific policy
 * methods.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $this->task = Task::create([
        'organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Task', 'priority' => 'medium', 'status' => 'pending',
    ]);
});

function makeAttachTestFolder(Organization $org, User $creator, string $name = 'Folder'): DocumentFolder
{
    return DocumentFolder::create(['organization_id' => $org->id, 'parent_id' => null, 'name' => $name, 'created_by' => $creator->id]);
}

function makeStaffWithDeptAccessForFolderTest(Organization $org, Department $dept): User
{
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $permissionId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $staffRole->permissions()->syncWithoutDetaching([$permissionId]);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $org->id, 'department_id' => $dept->id, 'allowed' => true]);

    return $staff;
}

test('management with manage_documents can attach a folder', function () {
    $folder = makeAttachTestFolder($this->orgA, $this->management);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folder->id]);

    $response->assertOk();
    expect($this->task->fresh()->folders()->pluck('document_folders.id')->all())->toBe([$folder->id]);
});

test('staff with manage_documents and department access can attach a folder', function () {
    $staff = makeStaffWithDeptAccessForFolderTest($this->orgA, $this->dept);
    $folder = makeAttachTestFolder($this->orgA, $this->management);

    $response = $this->actingAs($staff)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folder->id]);

    $response->assertOk();
    expect($this->task->fresh()->folders()->pluck('document_folders.id')->all())->toBe([$folder->id]);
});

test('owner can attach a folder', function () {
    $folder = makeAttachTestFolder($this->orgA, $this->management);

    $response = $this->actingAs($this->owner)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folder->id]);

    $response->assertOk();
});

test('a Client-role user with manage_documents is denied on both the button and the endpoint', function () {
    // "manage_documents stays tickable for Client" (task #73 phase 1) —
    // granted here specifically to prove the button/endpoint denial is
    // Client-role-based, not merely "no manage_documents".
    $clientRole = Role::where('slug', 'client')->firstOrFail();
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $clientRole->permissions()->syncWithoutDetaching([$manageDocumentsId]);

    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $client->id, 'role_id' => $clientRole->id]);
    $this->project->clients()->attach($client->id);

    $folder = makeAttachTestFolder($this->orgA, $this->management);

    $editPage = $this->actingAs($client)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect($editPage)->not->toContain('Attach folder');

    $response = $this->actingAs($client)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folder->id]);
    $response->assertForbidden();
    expect($this->task->fresh()->folders()->count())->toBe(0);
});

test('a user without manage_documents is denied', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    $folder = makeAttachTestFolder($this->orgA, $this->management);

    $response = $this->actingAs($staff)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folder->id]);

    $response->assertForbidden();
    expect($this->task->fresh()->folders()->count())->toBe(0);
});

test('a cross-company folder is rejected', function () {
    $folderInOrgB = makeAttachTestFolder($this->orgB, $this->owner, 'Org B folder');

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folderInOrgB->id]);

    $response->assertNotFound();
    expect($this->task->fresh()->folders()->count())->toBe(0);
});

test('a nonexistent folder id 404s', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => 999999]);

    $response->assertNotFound();
});

test('a duplicate attach is rejected with 422 and inserts no second row', function () {
    $folder = makeAttachTestFolder($this->orgA, $this->management);
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folder->id])->assertOk();

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/folders", ['folder_id' => $folder->id]);

    $response->assertStatus(422);
    expect($this->task->fresh()->folders()->count())->toBe(1);
});

test('detach removes only the task_folder_links row - the folder and its contents are untouched', function () {
    $folder = makeAttachTestFolder($this->orgA, $this->management);
    $document = Document::create([
        'organization_id' => $this->orgA->id, 'uploaded_by' => $this->management->id,
        'name' => 'Inside.pdf', 'link' => 'https://example.com/inside.pdf', 'access_level' => 'internal',
        'folder_id' => $folder->id,
    ]);
    $this->task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->deleteJson("/tasks/{$this->task->id}/folders/{$folder->id}");

    $response->assertOk();
    expect($this->task->fresh()->folders()->count())->toBe(0);
    expect(DocumentFolder::find($folder->id))->not->toBeNull();
    expect(Document::find($document->id))->not->toBeNull();
});

test('a user without manage_documents cannot detach a folder', function () {
    $folder = makeAttachTestFolder($this->orgA, $this->management);
    $this->task->folders()->attach($folder->id, ['linked_by' => $this->management->id]);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);

    $response = $this->actingAs($staff)->deleteJson("/tasks/{$this->task->id}/folders/{$folder->id}");

    $response->assertForbidden();
    expect($this->task->fresh()->folders()->count())->toBe(1);
});
