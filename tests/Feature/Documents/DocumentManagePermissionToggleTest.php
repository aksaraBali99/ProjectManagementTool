<?php

use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

/**
 * Covers the DocumentPolicy::create()/DocumentController::create() fix:
 * manage_documents is a real, live toggle in the Role Matrix for every
 * editable role (Management, Staff, Client), not something that only
 * ever works for Management regardless of what the matrix says. Staff is
 * the role that exercises this, since Management already holds
 * manage_documents by default (see PermissionSeeder) — Staff starts
 * without it, so granting/revoking it via the matrix is directly
 * observable here.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->staff->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    $this->staffRole = Role::where('slug', 'staff')->firstOrFail();
    $this->manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
});

/** Grants/revokes manage_documents for Staff via the real Role Matrix endpoint, keeping every other Staff grant untouched. */
function toggleStaffManageDocuments(User $owner, Role $staffRole, int $manageDocumentsId, bool $granted): void
{
    $currentIds = $staffRole->permissions()->pluck('permissions.id')->all();

    $newIds = $granted
        ? array_values(array_unique([...$currentIds, $manageDocumentsId]))
        : array_values(array_diff($currentIds, [$manageDocumentsId]));

    test()->actingAs($owner)->put('/roles/permissions', [
        'role_permissions' => [
            $staffRole->id => $newIds,
        ],
    ])->assertRedirect();
}

test('the Role Matrix checkbox for manage_documents / Staff persists across save and reload (sanity check, not a save-side bug)', function () {
    toggleStaffManageDocuments($this->owner, $this->staffRole, $this->manageDocumentsId, granted: true);

    expect($this->staffRole->fresh()->permissions()->pluck('slug')->all())->toContain('manage_documents');

    $page = $this->actingAs($this->owner)->get('/roles/permissions')->assertOk()->getContent();
    $checkboxPattern = '/<input type="checkbox"\s+name="role_permissions\['.$this->staffRole->id.'\]\[\]"\s+value="'.$this->manageDocumentsId.'"[^>]*checked[^>]*>/';
    expect(preg_match($checkboxPattern, $page))->toBe(1);

    toggleStaffManageDocuments($this->owner, $this->staffRole, $this->manageDocumentsId, granted: false);
    expect($this->staffRole->fresh()->permissions()->pluck('slug')->all())->not->toContain('manage_documents');
});

test('toggling manage_documents ON for Staff makes the Add Document button appear and actually usable', function () {
    toggleStaffManageDocuments($this->owner, $this->staffRole, $this->manageDocumentsId, granted: true);

    $this->actingAs($this->staff)->get('/documents/'.$this->org->id)
        ->assertOk()
        ->assertSee('+ Add new document');

    $this->actingAs($this->staff)->get('/documents/create/'.$this->org->id)
        ->assertOk()
        ->assertSee('Org A');

    $response = $this->actingAs($this->staff)->post('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Staff-added doc.pdf',
        'link' => 'https://example.com/staff-added.pdf',
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertRedirect('/documents/'.$this->org->id);
    $this->assertDatabaseHas('documents', [
        'name' => 'Staff-added doc.pdf',
        'uploaded_by' => $this->staff->id,
    ]);
});

test('toggling manage_documents OFF for Staff (after having been ON) hides the button and blocks the endpoint again', function () {
    toggleStaffManageDocuments($this->owner, $this->staffRole, $this->manageDocumentsId, granted: true);
    toggleStaffManageDocuments($this->owner, $this->staffRole, $this->manageDocumentsId, granted: false);

    $this->actingAs($this->staff)->get('/documents/'.$this->org->id)
        ->assertOk()
        ->assertDontSee('+ Add new document');

    $this->actingAs($this->staff)->get('/documents/create/'.$this->org->id)->assertForbidden();

    $response = $this->actingAs($this->staff)->post('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Blocked doc.pdf',
        'link' => 'https://example.com/blocked.pdf',
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('documents', ['name' => 'Blocked doc.pdf']);
});

test('without manage_documents, Staff never sees or can use the Add Document button/page/endpoint by default (regression guard)', function () {
    $this->actingAs($this->staff)->get('/documents/'.$this->org->id)
        ->assertOk()
        ->assertDontSee('+ Add new document');

    $this->actingAs($this->staff)->get('/documents/create/'.$this->org->id)->assertForbidden();

    $this->actingAs($this->staff)->post('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Default-blocked doc.pdf',
        'link' => 'https://example.com/default-blocked.pdf',
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ])->assertForbidden();
});
