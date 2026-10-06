<?php

use App\Models\AccessPermission;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\QueryException;

/**
 * task #73: detaching a document from a task used to be gated solely by
 * TaskPolicy::unlinkDocuments() — manage_documents + task view — which is
 * task-scoped and never sees the document. So ANY manage_documents holder
 * who could view the task could detach anyone else's document: a Staff
 * member removing one management attached, or a Client removing a Public
 * document staff attached to their project's task.
 *
 * TaskPolicy::detachDocument() adds the ownership half that
 * DocumentPolicy::canManage() already applies to rename/move/delete:
 * management tier overrides, otherwise you must be the uploader.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function grantManageDocumentsForDetach(string $roleSlug): void
{
    $role = Role::where('slug', $roleSlug)->firstOrFail();
    $role->permissions()->syncWithoutDetaching([
        Permission::where('slug', 'manage_documents')->firstOrFail()->id,
    ]);
}

/** Staff with manage_documents + department access: passes unlinkDocuments(), fails the management-tier override. */
function makeStaffForDetach(Organization $org, Department $department): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);
    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $org->id,
        'department_id' => $department->id,
        'allowed' => true,
    ]);
    grantManageDocumentsForDetach('staff');

    return $staff;
}

function makeClientForDetach(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->firstOrFail()->id,
    ]);
    $project->clients()->attach($client->id);
    grantManageDocumentsForDetach('client');

    return $client;
}

function makeDetachTestDocument(Organization $org, User $uploader, string $accessLevel = 'internal', string $name = 'Doc'): Document
{
    return Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => $name,
        'link' => 'https://example.com/'.uniqid().'.pdf',
        'access_level' => $accessLevel,
    ]);
}

test('staff cannot detach a document someone else uploaded: 403, link intact, nothing audited', function () {
    $staff = makeStaffForDetach($this->org, $this->dept);
    $document = makeDetachTestDocument($this->org, $this->management);
    $document->tasks()->attach($this->task->id);

    // Precondition: the task-level gate passes — only ownership fails.
    expect($staff->can('unlinkDocuments', $this->task))->toBeTrue();

    $this->actingAs($staff)
        ->deleteJson("/tasks/{$this->task->id}/documents/{$document->id}")
        ->assertForbidden();

    expect($this->task->fresh()->documents()->count())->toBe(1);
    expect(AuditLog::where('action', 'task.document_unlinked')->count())->toBe(0);
});

test('staff CAN detach a document they uploaded themselves', function () {
    $staff = makeStaffForDetach($this->org, $this->dept);
    $document = makeDetachTestDocument($this->org, $staff);
    $document->tasks()->attach($this->task->id);

    $this->actingAs($staff)
        ->deleteJson("/tasks/{$this->task->id}/documents/{$document->id}")
        ->assertOk();

    expect($this->task->fresh()->documents()->count())->toBe(0);
    // Still only unlinks — the document itself survives.
    $this->assertDatabaseHas('documents', ['id' => $document->id]);
    expect(AuditLog::where('action', 'task.document_unlinked')->count())->toBe(1);
});

test('management can detach a document a staff member uploaded', function () {
    $staff = makeStaffForDetach($this->org, $this->dept);
    $document = makeDetachTestDocument($this->org, $staff);
    $document->tasks()->attach($this->task->id);

    $this->actingAs($this->management)
        ->deleteJson("/tasks/{$this->task->id}/documents/{$document->id}")
        ->assertOk();

    expect($this->task->fresh()->documents()->count())->toBe(0);
});

test('a Client with manage_documents can detach only their own upload, not a staff-uploaded Public document', function () {
    $staff = makeStaffForDetach($this->org, $this->dept);
    $client = makeClientForDetach($this->org, $this->project);

    $staffDocument = makeDetachTestDocument($this->org, $staff, 'public', 'Staff upload');
    $clientDocument = makeDetachTestDocument($this->org, $client, 'public', 'Client upload');
    $staffDocument->tasks()->attach($this->task->id);
    $clientDocument->tasks()->attach($this->task->id);

    $this->actingAs($client)
        ->deleteJson("/tasks/{$this->task->id}/documents/{$staffDocument->id}")
        ->assertForbidden();

    $this->actingAs($client)
        ->deleteJson("/tasks/{$this->task->id}/documents/{$clientDocument->id}")
        ->assertOk();

    expect($this->task->fresh()->documents()->pluck('id')->all())->toBe([$staffDocument->id]);
});

test('a document can never have a null uploader, so detachDocument() needs no branch for one', function () {
    // The "uploader was deleted" case can't arise: uploaded_by is
    // foreignId()->constrained('users') — NOT NULL, with a restricting FK
    // (no nullOnDelete/cascadeOnDelete), so the column can't be nulled and
    // the uploader can't be deleted while any document still references
    // them. Pinned here so the missing null branch in
    // TaskPolicy::detachDocument() reads as "impossible", not "forgotten".
    $document = makeDetachTestDocument($this->org, $this->management);

    expect(fn () => $document->forceFill(['uploaded_by' => null])->save())
        ->toThrow(QueryException::class);
});

test('losing manage_documents denies the uploader too — the task-level gate still runs first', function () {
    $staff = makeStaffForDetach($this->org, $this->dept);
    $document = makeDetachTestDocument($this->org, $staff);
    $document->tasks()->attach($this->task->id);

    Role::where('slug', 'staff')->firstOrFail()->permissions()->detach(
        Permission::where('slug', 'manage_documents')->firstOrFail()->id
    );

    $this->actingAs($staff)
        ->deleteJson("/tasks/{$this->task->id}/documents/{$document->id}")
        ->assertForbidden();

    expect($this->task->fresh()->documents()->count())->toBe(1);
});

test('a private document uploaded by someone else is still 404, not 403 — the ordering is unchanged', function () {
    $staff = makeStaffForDetach($this->org, $this->dept);
    // Private + another uploader: invisible to this staff member, so it
    // must stay indistinguishable from "no such link" rather than
    // confirming it exists with a 403.
    $document = makeDetachTestDocument($this->org, $this->management, 'private');
    $document->tasks()->attach($this->task->id);

    $this->actingAs($staff)
        ->deleteJson("/tasks/{$this->task->id}/documents/{$document->id}")
        ->assertNotFound();

    expect($this->task->fresh()->documents()->count())->toBe(1);
});

test('the Detach button renders only on rows the viewer may actually detach', function () {
    $staff = makeStaffForDetach($this->org, $this->dept);
    $ownDocument = makeDetachTestDocument($this->org, $staff, 'internal', 'My own upload');
    $otherDocument = makeDetachTestDocument($this->org, $this->management, 'internal', 'Someone elses upload');
    $ownDocument->tasks()->attach($this->task->id);
    $otherDocument->tasks()->attach($this->task->id);

    $response = $this->actingAs($staff)->get("/tasks/{$this->task->id}/edit");
    $response->assertOk();

    expect($response->viewData('canUnlinkDocuments'))->toBeTrue();
    expect($response->viewData('canDetachAnyDocument'))->toBeFalse();

    // Asserted per ROW, not with assertSee('Detach') — this view's inline
    // script contains that same button markup as a JS template string, so
    // a page-wide text assertion would pass regardless of what rendered.
    expect(detachButtonPresentInRow($response->getContent(), $ownDocument->id))->toBeTrue();
    expect(detachButtonPresentInRow($response->getContent(), $otherDocument->id))->toBeFalse();

    // Management sees it on both.
    $managementResponse = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");
    expect($managementResponse->viewData('canDetachAnyDocument'))->toBeTrue();
    expect(detachButtonPresentInRow($managementResponse->getContent(), $ownDocument->id))->toBeTrue();
    expect(detachButtonPresentInRow($managementResponse->getContent(), $otherDocument->id))->toBeTrue();
});

/** Is there a .detach-document-btn inside the rendered row for this document id? */
function detachButtonPresentInRow(string $html, int $documentId): bool
{
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $buttons = $xpath->query(
        '//div[@data-document-id="'.$documentId.'"]'
        ."//button[contains(concat(' ', normalize-space(@class), ' '), ' detach-document-btn ')]"
    );

    return $buttons->length > 0;
}
