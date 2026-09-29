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
use App\Notifications\AuditEventDatabaseNotification;
use Illuminate\Support\Facades\DB;

/**
 * Covers task #73 phase 3's attach endpoint (POST /tasks/{task}/documents)
 * — the picker's action, and now the ONLY way to attach an existing
 * document to a task (the old <select>+button widget is gone).
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

function makeAttachTestDocument(Organization $org, User $uploader, string $accessLevel = 'internal'): Document
{
    return Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => 'Doc.pdf',
        'link' => 'https://example.com/doc.pdf',
        'access_level' => $accessLevel,
    ]);
}

function grantAttachPermission(Role $role, string $permissionSlug = 'manage_documents'): void
{
    $permissionId = Permission::where('slug', $permissionSlug)->firstOrFail()->id;
    $editableRoles = Role::whereIn('slug', ['management', 'staff', 'client'])->get();

    $payload = [];
    foreach ($editableRoles as $editableRole) {
        $currentIds = $editableRole->permissions()->pluck('permissions.id')->all();
        $payload[$editableRole->id] = $editableRole->is($role)
            ? array_values(array_unique([...$currentIds, $permissionId]))
            : $currentIds;
    }

    $grantOwner = User::factory()->create();
    $grantOwner->roles()->attach(Role::where('slug', 'owner')->firstOrFail()->id);

    test()->actingAs($grantOwner)->put('/roles/permissions', ['role_permissions' => $payload])->assertRedirect();
}

test('attaches an internal document and returns it with a computed url', function () {
    $document = makeAttachTestDocument($this->orgA, $this->management);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id]);

    $response->assertOk();
    expect($response->json('document.id'))->toBe($document->id);
    expect($response->json('document.url'))->not->toBeNull();
    expect($this->task->documents()->pluck('documents.id')->all())->toBe([$document->id]);
});

test('appears in the task\'s document list after attaching, without a reload (endpoint-level: the edit page reflects it)', function () {
    $document = makeAttachTestDocument($this->orgA, $this->management);
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id])->assertOk();

    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");
    $response->assertOk()->assertSee($document->name);
});

test('a private document is rejected with a 422, even by direct request', function () {
    $document = makeAttachTestDocument($this->orgA, $this->management, 'private');

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id]);

    $response->assertStatus(422);
    expect($response->json('message'))->toBe('Private documents can\'t be attached to tasks. Change its access level first.');
    expect($this->task->documents()->count())->toBe(0);
});

test('a document from another company gets 404, including for owner', function () {
    $document = makeAttachTestDocument($this->orgB, $this->owner);

    $response = $this->actingAs($this->owner)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id]);

    $response->assertNotFound();
});

test('a document the viewer cannot see gets 404', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Ops', 'color' => '#111']);
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    grantAttachPermission($staffRole);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);

    // Staff without view_documents can't view() an Internal document
    // uploaded by someone else.
    $viewDocumentsId = Permission::where('slug', 'view_documents')->firstOrFail()->id;
    $staffRole->permissions()->detach($viewDocumentsId);

    $document = makeAttachTestDocument($this->orgA, $this->management);

    $this->actingAs($staff)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id])->assertNotFound();
});

test('a nonexistent document id gets 404', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => 999999])->assertNotFound();
});

test('a duplicate attach inserts no second row and returns 422', function () {
    $document = makeAttachTestDocument($this->orgA, $this->management);
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id])->assertOk();

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id]);

    $response->assertStatus(422);
    expect($response->json('message'))->toBe('Already attached.');
    expect($this->task->documents()->count())->toBe(1);
});

test('a document switched to private after listing but before attaching is rejected', function () {
    $document = makeAttachTestDocument($this->orgA, $this->management);

    // Simulate the race: the document is switched to private directly in
    // the database (bypassing the endpoint's own block, exactly as a
    // concurrent request from a different browser tab would) between
    // when the picker listed it and when this attach request arrives.
    $document->access_level = 'private';
    $document->save();

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id]);

    $response->assertStatus(422);
    expect($response->json('message'))->toBe('Private documents can\'t be attached to tasks. Change its access level first.');
    expect($this->task->documents()->count())->toBe(0);
});

test('a document moved to another company after the initial checks but before the lock is rejected, not attached', function () {
    // Regression guard: the transaction's locked re-check used to
    // re-verify only access_level, trusting the earlier organization-
    // match check as still true by the time the lock is taken - even
    // though that's exactly the kind of thing a concurrent action (an
    // org reassignment) could invalidate in between. The actor here must
    // be an owner/super_admin: for anyone else, Document's own
    // BelongsToOrganization global scope already 404s a cross-company
    // document on the locked re-query regardless of this explicit check
    // (see attach()'s own docblock) - only owner/super_admin bypass that
    // scope, which is exactly why the explicit check exists for them.
    // Simulated the same way the existing "switched to private" race
    // test does (a direct DB mutation standing in for a concurrent
    // request), but timed to land between the two Document reads
    // specifically, via Eloquent's own `retrieved` event - the second
    // retrieval is the locked one inside the transaction.
    $document = makeAttachTestDocument($this->orgA, $this->management);

    $retrievals = 0;
    Document::retrieved(function (Document $retrieved) use (&$retrievals, $document) {
        if ($retrieved->id !== $document->id) {
            return;
        }
        $retrievals++;
        // Fires after the FIRST retrieval (the pre-transaction check) so
        // the mutation lands in time for the SECOND retrieval (the locked
        // re-check inside the transaction) to see it - the `retrieved`
        // event fires post-hydration, using data already read by that
        // query, so mutating on retrieval N only affects retrieval N+1.
        if ($retrievals === 1) {
            DB::table('documents')->where('id', $document->id)->update(['organization_id' => $this->orgB->id]);
        }
    });

    $response = $this->actingAs($this->owner)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id]);

    $response->assertNotFound();
    expect($this->task->documents()->count())->toBe(0);
});

test('manage_documents OFF hides the button and 403s the attach endpoint for staff', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    $document = makeAttachTestDocument($this->orgA, $this->management);

    $this->actingAs($staff)->get("/tasks/{$this->task->id}/edit")->assertOk()->assertDontSee('Attach document');
    $this->actingAs($staff)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id])->assertForbidden();
});

test('manage_documents ON shows the button and allows the attach endpoint for staff', function () {
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    grantAttachPermission($staffRole);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    $document = makeAttachTestDocument($this->orgA, $this->management);

    $this->actingAs($staff)->get("/tasks/{$this->task->id}/edit")->assertOk()->assertSee('Attach document');
    $this->actingAs($staff)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id])->assertOk();
});

test('a user with manage_documents but no view on the task is denied the attach endpoint', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Finance', 'color' => '#111']);
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    grantAttachPermission($staffRole);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => $staffRole->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $otherDept->id, 'allowed' => true]);
    $document = makeAttachTestDocument($this->orgA, $this->management);

    $this->actingAs($staff)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id])->assertForbidden();
});

test('a Client-role user with manage_documents is denied on the attach endpoint and sees no button', function () {
    $clientRole = Role::where('slug', 'client')->firstOrFail();
    grantAttachPermission($clientRole);

    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $client->id, 'role_id' => $clientRole->id]);
    $this->project->clients()->attach($client->id);
    $document = makeAttachTestDocument($this->orgA, $this->management);

    $this->actingAs($client)->get("/tasks/{$this->task->id}/edit")->assertOk()->assertDontSee('Attach document');
    $this->actingAs($client)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id])->assertForbidden();
});

test('attach writes a task.document_attached audit entry with the document id and name, and never notifies', function () {
    $document = makeAttachTestDocument($this->orgA, $this->management);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/documents", ['document_id' => $document->id])->assertOk();

    $entry = AuditLog::where('action', 'task.document_attached')->where('entity_id', $this->task->id)->first();
    expect($entry)->not->toBeNull();
    expect($entry->entity_type)->toBe('task');
    expect($entry->changes)->toBe(['document_id' => $document->id, 'document_name' => $document->name]);

    $this->assertDatabaseMissing('notifications', ['type' => AuditEventDatabaseNotification::class]);
});

test('detaching removes only the link and leaves the document, and writes a task.document_unlinked audit entry', function () {
    $document = makeAttachTestDocument($this->orgA, $this->management);
    $this->task->documents()->attach($document->id);

    $response = $this->actingAs($this->management)->deleteJson("/tasks/{$this->task->id}/documents/{$document->id}");

    $response->assertOk();
    expect($this->task->documents()->count())->toBe(0);
    $this->assertDatabaseHas('documents', ['id' => $document->id]);

    $entry = AuditLog::where('action', 'task.document_unlinked')->where('entity_id', $this->task->id)->first();
    expect($entry)->not->toBeNull();
    expect($entry->changes)->toBe(['document_id' => $document->id, 'document_name' => $document->name]);
});
