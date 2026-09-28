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

beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

function makeStaffForEditTest(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    return $staff;
}

function makeClientForEditTest(Organization $org): User
{
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);

    return $client;
}

function makeDocumentForEditTest(Organization $org, User $uploader, string $level = 'internal', ?int $folderId = null): Document
{
    return Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => 'Original name',
        'link' => 'https://example.com/original.pdf',
        'access_level' => $level,
        'folder_id' => $folderId,
    ]);
}

/**
 * The real Role Matrix form always submits every editable role's
 * checkboxes together in one POST — PermissionManagementController::
 * update() treats any editable role missing from the payload as "no
 * permissions submitted" and syncs it to empty, so a partial payload
 * naming only $role would silently wipe every OTHER editable role's
 * grants too. Preserve them explicitly, matching what the browser
 * actually sends.
 */
function grantManageDocumentsForEditTest(Role $role): void
{
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $editableRoles = Role::whereIn('slug', ['management', 'staff', 'client'])->get();

    $payload = [];
    foreach ($editableRoles as $editableRole) {
        $currentIds = $editableRole->permissions()->pluck('permissions.id')->all();
        $payload[$editableRole->id] = $editableRole->is($role)
            ? array_values(array_unique([...$currentIds, $manageDocumentsId]))
            : $currentIds;
    }

    $grantOwner = User::factory()->create();
    $grantOwner->roles()->attach(Role::where('slug', 'owner')->firstOrFail()->id);

    test()->actingAs($grantOwner)->put('/roles/permissions', ['role_permissions' => $payload])->assertRedirect();
}

test('the uploader can rename their own document', function () {
    $staff = makeStaffForEditTest($this->orgA);
    grantManageDocumentsForEditTest(Role::where('slug', 'staff')->firstOrFail());
    $document = makeDocumentForEditTest($this->orgA, $staff);

    $response = $this->actingAs($staff)->putJson("/documents/{$document->id}", ['name' => 'New name']);

    $response->assertOk();
    expect($document->fresh()->name)->toBe('New name');
    $this->assertDatabaseHas('audit_log', ['action' => 'document.renamed', 'entity_id' => $document->id]);
});

test('a non-uploader staff member cannot edit someone else\'s document, even with manage_documents', function () {
    $uploader = makeStaffForEditTest($this->orgA);
    $otherStaff = makeStaffForEditTest($this->orgA);
    grantManageDocumentsForEditTest(Role::where('slug', 'staff')->firstOrFail());
    $document = makeDocumentForEditTest($this->orgA, $uploader);

    $this->actingAs($otherStaff)->putJson("/documents/{$document->id}", ['name' => 'Hijacked'])->assertForbidden();
    expect($document->fresh()->name)->toBe('Original name');
});

test('management can edit a document they did not upload', function () {
    $staff = makeStaffForEditTest($this->orgA);
    $document = makeDocumentForEditTest($this->orgA, $staff);

    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['name' => 'Renamed by management'])->assertOk();
});

test('owner can edit any document regardless of manage_documents or uploader', function () {
    $staff = makeStaffForEditTest($this->orgA);
    $document = makeDocumentForEditTest($this->orgA, $staff);

    $this->actingAs($this->owner)->putJson("/documents/{$document->id}", ['name' => 'Renamed by owner'])->assertOk();
});

test('a Client-role user can never edit a document, even their own upload', function () {
    $client = makeClientForEditTest($this->orgA);
    // manage_documents CAN be granted to Client (task #73 phase 1), but
    // edit/delete are unconditionally denied to Client regardless.
    $document = makeDocumentForEditTest($this->orgA, $client);

    $this->actingAs($client)->putJson("/documents/{$document->id}", ['name' => 'Client edit'])->assertForbidden();
    expect($document->fresh()->name)->toBe('Original name');
});

test('a document can move into a folder and back to the root', function () {
    $document = makeDocumentForEditTest($this->orgA, $this->management);
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);

    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => $folder->id])->assertOk();
    expect($document->fresh()->folder_id)->toBe($folder->id);
    $this->assertDatabaseHas('audit_log', ['action' => 'document.moved', 'entity_id' => $document->id]);

    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => null])->assertOk();
    expect($document->fresh()->folder_id)->toBeNull();
});

test('a cross-company folder_id on move is rejected', function () {
    $document = makeDocumentForEditTest($this->orgA, $this->management);
    $folderInOrgB = DocumentFolder::create(['organization_id' => $this->orgB->id, 'name' => 'Org B folder', 'created_by' => $this->owner->id]);

    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['folder_id' => $folderInOrgB->id])->assertStatus(404);
    expect($document->fresh()->folder_id)->toBeNull();
});

test('changing access level to private is blocked while the document is directly linked to a task, listing viewable tasks and hiding the rest as a count', function () {
    $viewableTask = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'Visible task', 'priority' => 'medium', 'status' => 'pending']);

    $otherOrgProject = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Other project', 'description' => 'd']);
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Ops', 'color' => '#111']);
    $hiddenTask = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $otherOrgProject->id, 'department_id' => $otherDept->id, 'title' => 'Hidden task', 'priority' => 'medium', 'status' => 'pending']);

    // A staff viewer with department access to $this->dept only — sees
    // $viewableTask, not $hiddenTask. Uploaded by this same staff member,
    // so they pass canManage()'s "uploader OR management" clause (a
    // non-uploader, non-management staff member is denied outright
    // regardless of task visibility — see the earlier authorization tests).
    $staff = makeStaffForEditTest($this->orgA);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    grantManageDocumentsForEditTest(Role::where('slug', 'staff')->firstOrFail());

    $document = makeDocumentForEditTest($this->orgA, $staff, 'internal');
    $document->tasks()->attach([$viewableTask->id, $hiddenTask->id]);

    $response = $this->actingAs($staff)->putJson("/documents/{$document->id}", ['access_level' => 'private']);

    $response->assertStatus(422);
    expect($response->json('message'))->toBe('This document is still attached to 2 tasks. Remove it from them first.');
    expect($response->json('linked_tasks'))->toBe([['id' => $viewableTask->id, 'title' => 'Visible task']]);
    expect($response->json('hidden_linked_task_count'))->toBe(1);
    expect($document->fresh()->access_level->value)->toBe('internal');
});

test('changing access level to private is allowed once nothing is linked', function () {
    $document = makeDocumentForEditTest($this->orgA, $this->management, 'internal');

    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['access_level' => 'private'])->assertOk();
    expect($document->fresh()->access_level->value)->toBe('private');
    $this->assertDatabaseHas('audit_log', ['action' => 'document.access_level_changed', 'entity_id' => $document->id]);
});

test('changing access level to public requires confirmation when linked to a project with a client, and is rejected without it', function () {
    $client = makeClientForEditTest($this->orgA);
    $this->project->clients()->attach($client->id);
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);

    $document = makeDocumentForEditTest($this->orgA, $this->management, 'internal');
    $document->tasks()->attach($task->id);

    $withoutConfirm = $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['access_level' => 'public']);
    $withoutConfirm->assertStatus(422);
    expect($withoutConfirm->json('requires_confirmation'))->toBeTrue();
    expect($withoutConfirm->json('client_project_count'))->toBe(1);
    expect($withoutConfirm->json('message'))->toBe('Visible to the clients of 1 linked projects.');
    expect($document->fresh()->access_level->value)->toBe('internal');

    $withConfirm = $this->actingAs($this->management)->putJson("/documents/{$document->id}", [
        'access_level' => 'public',
        'confirm_public_visibility' => true,
    ]);
    $withConfirm->assertOk();
    expect($document->fresh()->access_level->value)->toBe('public');
    $this->assertDatabaseHas('audit_log', ['action' => 'document.access_level_changed', 'entity_id' => $document->id]);
});

test('changing access level to public needs no confirmation when N is 0 (no linked project has a client)', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = makeDocumentForEditTest($this->orgA, $this->management, 'internal');
    $document->tasks()->attach($task->id);

    // $this->project has no client attached at all.
    $response = $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['access_level' => 'public']);

    $response->assertOk();
    expect($document->fresh()->access_level->value)->toBe('public');
});

test('renaming, moving, or changing access level between two non-blocking levels needs no confirmation and is not restricted', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = makeDocumentForEditTest($this->orgA, $this->management, 'internal');
    $document->tasks()->attach($task->id);

    // internal -> public with no client on the project is fine (covered
    // above); internal <-> internal-adjacent changes like name/folder
    // alone, while linked, are entirely unrestricted.
    $this->actingAs($this->management)->putJson("/documents/{$document->id}", ['name' => 'Renamed while linked'])->assertOk();
    expect($document->fresh()->name)->toBe('Renamed while linked');
});
