<?php

use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);
});

function makeStaffForFolderTest(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    return $staff;
}

function grantManageDocuments(Role $role, ?int $extraPermissionId = null): void
{
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $currentIds = $role->permissions()->pluck('permissions.id')->all();
    $newIds = array_values(array_unique(array_filter([...$currentIds, $manageDocumentsId, $extraPermissionId])));

    // The real Role Matrix form always submits every editable role's
    // checkboxes together in one POST — PermissionManagementController::
    // update() treats any editable role missing from the payload as "no
    // permissions submitted" and syncs it to empty, so a partial payload
    // naming only $role would silently wipe every OTHER editable role's
    // grants too. Preserve them explicitly, matching what the browser
    // actually sends.
    $editableRoles = Role::whereIn('slug', ['management', 'staff', 'client'])->get();
    $payload = [];
    foreach ($editableRoles as $editableRole) {
        $payload[$editableRole->id] = $editableRole->is($role)
            ? $newIds
            : $editableRole->permissions()->pluck('permissions.id')->all();
    }

    test()->actingAs(createOwnerForGrant())->put('/roles/permissions', [
        'role_permissions' => $payload,
    ])->assertRedirect();
}

/** A throwaway owner, so grantManageDocuments() doesn't depend on the calling test's own $this->owner already existing. */
function createOwnerForGrant(): User
{
    $owner = User::factory()->create();
    $owner->roles()->attach(Role::where('slug', 'owner')->firstOrFail()->id);

    return $owner;
}

test('a manage_documents holder can create a root folder and a nested subfolder', function () {
    $response = $this->actingAs($this->management)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'name' => 'Contracts',
    ]);
    $response->assertCreated();
    $root = DocumentFolder::where('name', 'Contracts')->firstOrFail();
    expect($root->parent_id)->toBeNull();
    expect($root->created_by)->toBe($this->management->id);

    $nested = $this->actingAs($this->management)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'parent_id' => $root->id,
        'name' => '2026',
    ]);
    $nested->assertCreated();
    $nestedFolder = DocumentFolder::where('name', '2026')->firstOrFail();
    expect($nestedFolder->parent_id)->toBe($root->id);
});

test('a folder can be renamed', function () {
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Old name', 'created_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->putJson("/document-folders/{$folder->id}", ['name' => 'New name']);

    $response->assertOk();
    expect($folder->fresh()->name)->toBe('New name');
    $this->assertDatabaseHas('audit_log', ['action' => 'folder.renamed', 'entity_id' => $folder->id]);
});

test('a duplicate sibling name is rejected at the root and inside a folder, case-insensitively', function () {
    DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);

    $this->actingAs($this->management)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'name' => 'CONTRACTS',
    ])->assertStatus(422);

    $parent = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Parent', 'created_by' => $this->management->id]);
    DocumentFolder::create(['organization_id' => $this->orgA->id, 'parent_id' => $parent->id, 'name' => 'Child', 'created_by' => $this->management->id]);

    $this->actingAs($this->management)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'parent_id' => $parent->id,
        'name' => 'child',
    ])->assertStatus(422);

    // A sibling name under a DIFFERENT parent is fine.
    $this->actingAs($this->management)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'name' => 'Child',
    ])->assertCreated();
});

test('renaming a folder to its own current name (case-only) is not rejected as a duplicate of itself', function () {
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);

    $this->actingAs($this->management)->putJson("/document-folders/{$folder->id}", ['name' => 'Contracts'])->assertOk();
});

test('a non-empty folder cannot be deleted, including one holding a document the deleter cannot see, and the message reveals nothing', function () {
    // A plain Staff user, not management/owner/super_admin — those three
    // can unconditionally view ANY document regardless of access_level
    // (DocumentPolicy::view()'s own first branch), so they can never
    // stand in for "a viewer who genuinely can't see what's inside".
    $staff = makeStaffForFolderTest($this->orgA);
    grantManageDocuments(Role::where('slug', 'staff')->firstOrFail());

    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Contracts', 'created_by' => $staff->id]);
    $privateDocument = Document::create([
        'organization_id' => $this->orgA->id,
        'uploaded_by' => $this->owner->id,
        'name' => 'Secret',
        'link' => 'https://example.com/secret.pdf',
        'access_level' => 'private',
        'folder_id' => $folder->id,
    ]);

    // Sanity: staff (view_documents, not the uploader) genuinely cannot
    // view a private document uploaded by someone else — the folder-
    // delete check still counts it anyway.
    expect($staff->can('view', $privateDocument))->toBeFalse();

    $response = $this->actingAs($staff)->deleteJson("/document-folders/{$folder->id}");

    $response->assertStatus(422);
    expect($response->json('message'))->toBe("This folder isn't empty.");
    expect($response->json('message'))->not->toContain('Secret');
    expect($response->json())->not->toHaveKey('count');
    $this->assertDatabaseHas('document_folders', ['id' => $folder->id]);
});

test('a folder with a child folder cannot be deleted', function () {
    $parent = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Parent', 'created_by' => $this->management->id]);
    DocumentFolder::create(['organization_id' => $this->orgA->id, 'parent_id' => $parent->id, 'name' => 'Child', 'created_by' => $this->management->id]);

    $this->actingAs($this->management)->deleteJson("/document-folders/{$parent->id}")->assertStatus(422);
    $this->assertDatabaseHas('document_folders', ['id' => $parent->id]);
});

test('an empty folder deletes', function () {
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Contracts', 'created_by' => $this->management->id]);

    $response = $this->actingAs($this->management)->deleteJson("/document-folders/{$folder->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('document_folders', ['id' => $folder->id]);
    $this->assertDatabaseHas('audit_log', ['action' => 'folder.deleted']);
});

test('the folder\'s own creator can rename and delete it', function () {
    $staff = makeStaffForFolderTest($this->orgA);
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    grantManageDocuments($staffRole);

    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Mine', 'created_by' => $staff->id]);

    $this->actingAs($staff)->putJson("/document-folders/{$folder->id}", ['name' => 'Renamed'])->assertOk();
    $this->actingAs($staff)->deleteJson("/document-folders/{$folder->id}")->assertOk();
});

test('management can rename and delete a folder they did not create', function () {
    $creator = makeStaffForFolderTest($this->orgA);
    grantManageDocuments(Role::where('slug', 'staff')->firstOrFail());
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Theirs', 'created_by' => $creator->id]);

    $this->actingAs($this->management)->putJson("/document-folders/{$folder->id}", ['name' => 'Renamed by management'])->assertOk();

    $folder2 = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Theirs again', 'created_by' => $creator->id]);
    $this->actingAs($this->management)->deleteJson("/document-folders/{$folder2->id}")->assertOk();
});

test('owner can rename and delete any folder regardless of manage_documents or creator', function () {
    $creator = makeStaffForFolderTest($this->orgA);
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Theirs', 'created_by' => $creator->id]);

    $this->actingAs($this->owner)->putJson("/document-folders/{$folder->id}", ['name' => 'Renamed by owner'])->assertOk();

    $folder2 = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Theirs again', 'created_by' => $creator->id]);
    $this->actingAs($this->owner)->deleteJson("/document-folders/{$folder2->id}")->assertOk();
});

test('a non-creator staff member with manage_documents cannot rename or delete someone else\'s folder', function () {
    $creator = makeStaffForFolderTest($this->orgA);
    $otherStaff = makeStaffForFolderTest($this->orgA);
    grantManageDocuments(Role::where('slug', 'staff')->firstOrFail());

    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Not yours', 'created_by' => $creator->id]);

    $this->actingAs($otherStaff)->putJson("/document-folders/{$folder->id}", ['name' => 'Hijacked'])->assertForbidden();
    $this->actingAs($otherStaff)->deleteJson("/document-folders/{$folder->id}")->assertForbidden();
    expect($folder->fresh()->name)->toBe('Not yours');
});

test('a cross-company parent_id is rejected on create, and a cross-company folder 404s on rename/delete', function () {
    $folderInOrgB = DocumentFolder::create(['organization_id' => $this->orgB->id, 'name' => 'Org B folder', 'created_by' => $this->owner->id]);

    $this->actingAs($this->management)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'parent_id' => $folderInOrgB->id,
        'name' => 'Should fail',
    ])->assertStatus(404);

    // management (Org A only) isn't a member of Org B at all, so
    // DocumentFolder's own BelongsToOrganization global scope excludes
    // this row before route-model binding ever resolves it — a plain
    // 404, the same as any other tenant-scoped lookup, not a 403 from the
    // policy layer. Either way, nothing about Org B's folder is exposed
    // or modified.
    $this->actingAs($this->management)->putJson("/document-folders/{$folderInOrgB->id}", ['name' => 'Hijacked'])->assertNotFound();
});

test('manage_documents OFF for Staff blocks folder create/rename/delete endpoints and hides the buttons', function () {
    $staff = makeStaffForFolderTest($this->orgA);
    $folder = DocumentFolder::create(['organization_id' => $this->orgA->id, 'name' => 'Existing', 'created_by' => $staff->id]);

    $this->actingAs($staff)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'name' => 'Blocked',
    ])->assertForbidden();
    $this->actingAs($staff)->putJson("/document-folders/{$folder->id}", ['name' => 'Blocked rename'])->assertForbidden();
    $this->actingAs($staff)->deleteJson("/document-folders/{$folder->id}")->assertForbidden();
});

test('manage_documents ON for Staff allows folder create/rename/delete', function () {
    $staff = makeStaffForFolderTest($this->orgA);
    grantManageDocuments(Role::where('slug', 'staff')->firstOrFail());

    $this->actingAs($staff)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'name' => 'Allowed',
    ])->assertCreated();

    $folder = DocumentFolder::where('name', 'Allowed')->firstOrFail();
    $this->actingAs($staff)->putJson("/document-folders/{$folder->id}", ['name' => 'Allowed renamed'])->assertOk();
    $this->actingAs($staff)->deleteJson("/document-folders/{$folder->id}")->assertOk();
});

test('a folder name is required and whitespace-only is rejected', function () {
    $this->actingAs($this->management)->postJson('/document-folders', [
        'organization_id' => $this->orgA->id,
        'name' => '   ',
    ])->assertStatus(422);
});
