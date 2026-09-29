<?php

use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Policies\DocumentPolicy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

/**
 * task #73 phase 3: DocumentPolicy::attachableInCompany() is NOT a general
 * view() stand-in (see its own docblock) — it's the picker's narrow query,
 * valid only for a non-Client caller and only over Internal/Public
 * documents. This asserts it agrees with the real view() across every
 * role the picker can actually present it with, in both the queried
 * company and another one, for both access levels it ever returns.
 * Private documents are deliberately excluded from this matrix — the
 * method excludes them unconditionally by design, which would disagree
 * with view() for a document's own uploader, and that disagreement is the
 * intended narrowing, not a bug this test should chase.
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);

    $this->uploader = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->uploader->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
});

function makeAttachableDocumentSet(Organization $org, User $uploader): Collection
{
    return collect([
        Document::create(['organization_id' => $org->id, 'uploaded_by' => $uploader->id, 'name' => 'Internal', 'link' => 'https://example.com/internal.pdf', 'access_level' => 'internal']),
        Document::create(['organization_id' => $org->id, 'uploaded_by' => $uploader->id, 'name' => 'Public', 'link' => 'https://example.com/public.pdf', 'access_level' => 'public']),
    ]);
}

/** Asserts Gate::allows('view', ...) and attachableInCompany() agree, per document, in $organizationId. */
function assertAttachableParity(User $user, Collection $documents, int $organizationId): void
{
    $attachableIds = app(DocumentPolicy::class)->attachableInCompany($user, $organizationId)->pluck('id')->all();

    foreach ($documents as $document) {
        $gateResult = Gate::forUser($user)->allows('view', $document);
        $attachableResult = in_array($document->id, $attachableIds, true);

        expect($attachableResult)->toBe($gateResult, "Mismatch for document [{$document->name}] ({$document->access_level->value}) in org {$organizationId}");
    }
}

test('super_admin: agrees with view() in the same company and another one', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->roles()->attach(Role::where('slug', 'super_admin')->firstOrFail()->id);

    $inOrgA = makeAttachableDocumentSet($this->orgA, $this->uploader);
    $inOrgB = makeAttachableDocumentSet($this->orgB, $this->uploader);

    assertAttachableParity($superAdmin, $inOrgA, $this->orgA->id);
    assertAttachableParity($superAdmin, $inOrgB, $this->orgB->id);
    expect(app(DocumentPolicy::class)->attachableInCompany($superAdmin, $this->orgA->id)->get())->toHaveCount(2);
});

test('owner: agrees with view() in the same company and another one', function () {
    $inOrgA = makeAttachableDocumentSet($this->orgA, $this->uploader);
    $inOrgB = makeAttachableDocumentSet($this->orgB, $this->uploader);

    assertAttachableParity($this->owner, $inOrgA, $this->orgA->id);
    assertAttachableParity($this->owner, $inOrgB, $this->orgB->id);
    expect(app(DocumentPolicy::class)->attachableInCompany($this->owner, $this->orgA->id)->get())->toHaveCount(2);
});

test('management: agrees with view() in their own company and sees nothing in another', function () {
    $management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $inOrgA = makeAttachableDocumentSet($this->orgA, $this->uploader);
    $inOrgB = makeAttachableDocumentSet($this->orgB, $this->uploader);

    assertAttachableParity($management, $inOrgA, $this->orgA->id);
    assertAttachableParity($management, $inOrgB, $this->orgB->id);
    expect(app(DocumentPolicy::class)->attachableInCompany($management, $this->orgA->id)->get())->toHaveCount(2);
    expect(app(DocumentPolicy::class)->attachableInCompany($management, $this->orgB->id)->get())->toBeEmpty();
});

test('staff with view_documents: agrees with view() in their own company and sees nothing in another', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);

    $inOrgA = makeAttachableDocumentSet($this->orgA, $this->uploader);
    $inOrgB = makeAttachableDocumentSet($this->orgB, $this->uploader);

    assertAttachableParity($staff, $inOrgA, $this->orgA->id);
    assertAttachableParity($staff, $inOrgB, $this->orgB->id);
    expect(app(DocumentPolicy::class)->attachableInCompany($staff, $this->orgA->id)->get())->toHaveCount(2);
    expect(app(DocumentPolicy::class)->attachableInCompany($staff, $this->orgB->id)->get())->toBeEmpty();
});

test('staff without view_documents: agrees with view() (sees nothing) in either company', function () {
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $viewDocumentsId = Permission::where('slug', 'view_documents')->firstOrFail()->id;
    $staffRole->permissions()->detach($viewDocumentsId);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);

    $inOrgA = makeAttachableDocumentSet($this->orgA, $this->uploader);
    $inOrgB = makeAttachableDocumentSet($this->orgB, $this->uploader);

    assertAttachableParity($staff, $inOrgA, $this->orgA->id);
    assertAttachableParity($staff, $inOrgB, $this->orgB->id);
    expect(app(DocumentPolicy::class)->attachableInCompany($staff, $this->orgA->id)->get())->toBeEmpty();
});

test('a user holding manage_documents but not view_documents: agrees with view() (sees nothing)', function () {
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $viewDocumentsId = Permission::where('slug', 'view_documents')->firstOrFail()->id;
    $staffRole->permissions()->detach($viewDocumentsId);
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $staffRole->permissions()->syncWithoutDetaching([$manageDocumentsId]);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);

    $inOrgA = makeAttachableDocumentSet($this->orgA, $this->uploader);

    assertAttachableParity($staff, $inOrgA, $this->orgA->id);
    expect(app(DocumentPolicy::class)->attachableInCompany($staff, $this->orgA->id)->get())->toBeEmpty();
});

test('never returns a private document, regardless of role', function () {
    $private = Document::create(['organization_id' => $this->orgA->id, 'uploaded_by' => $this->uploader->id, 'name' => 'Private', 'link' => 'https://example.com/private.pdf', 'access_level' => 'private']);

    $ids = app(DocumentPolicy::class)->attachableInCompany($this->owner, $this->orgA->id)->pluck('id');

    expect($ids)->not->toContain($private->id);
});
