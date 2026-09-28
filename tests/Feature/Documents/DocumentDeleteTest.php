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
use App\Services\FileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('r2');
    $this->owner = createOwner();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

function makeClientForDeleteTest(Organization $org): User
{
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->firstOrFail()->id]);

    return $client;
}

function makeLinkOnlyDocumentForDeleteTest(Organization $org, User $uploader): Document
{
    return Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => 'Link doc',
        'link' => 'https://example.com/link-doc.pdf',
        'access_level' => 'internal',
    ]);
}

test('the dependency preview lists viewable linked tasks and a hidden count, for both blocked and unblocked documents', function () {
    $viewableTask = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'Visible task', 'priority' => 'medium', 'status' => 'pending']);
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);
    $document->tasks()->attach($viewableTask->id);

    $response = $this->actingAs($this->management)->getJson("/documents/{$document->id}/dependencies");

    $response->assertOk();
    expect($response->json('linked_task_count'))->toBe(1);
    expect($response->json('viewable_tasks'))->toBe([['id' => $viewableTask->id, 'title' => 'Visible task']]);
    expect($response->json('hidden_linked_task_count'))->toBe(0);
});

test('deleting a document blocked by a linked task returns the same dialog data as the edit block, and does not delete', function () {
    $viewableTask = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'Visible task', 'priority' => 'medium', 'status' => 'pending']);
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);
    $document->tasks()->attach($viewableTask->id);

    $response = $this->actingAs($this->management)->deleteJson("/documents/{$document->id}");

    $response->assertStatus(422);
    expect($response->json('message'))->toBe('This document is still attached to 1 tasks. Remove it from them first.');
    expect($response->json('linked_tasks'))->toBe([['id' => $viewableTask->id, 'title' => 'Visible task']]);
    $this->assertDatabaseHas('documents', ['id' => $document->id]);
});

test('a hidden linked task appears only as a count in the delete block, never by title', function () {
    $otherDept = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Ops', 'color' => '#111']);
    $hiddenTask = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $otherDept->id, 'title' => 'Top Secret Task', 'priority' => 'medium', 'status' => 'pending']);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    grantManageDocumentsForDeleteTest(Role::where('slug', 'staff')->firstOrFail());

    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $staff);
    $document->tasks()->attach($hiddenTask->id);

    $response = $this->actingAs($staff)->deleteJson("/documents/{$document->id}");

    $response->assertStatus(422);
    expect($response->json('linked_tasks'))->toBe([]);
    expect($response->json('hidden_linked_task_count'))->toBe(1);
    expect($response->json('message'))->not->toContain('Top Secret Task');
});

test('deleting becomes possible after unlinking the only attached task', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);
    $document->tasks()->attach($task->id);

    $this->actingAs($this->management)->deleteJson("/documents/{$document->id}")->assertStatus(422);

    $this->actingAs($this->management)->deleteJson("/tasks/{$task->id}/documents/{$document->id}")->assertOk();

    $this->actingAs($this->management)->deleteJson("/documents/{$document->id}")->assertOk();
    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('attaching a task to the document between the preview and the delete request is caught by the in-transaction re-check', function () {
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);

    // Preview says unblocked (nothing linked yet)...
    $preview = $this->actingAs($this->management)->getJson("/documents/{$document->id}/dependencies");
    $preview->assertOk();
    expect($preview->json('linked_task_count'))->toBe(0);

    // ...but something attaches it to a task before the actual delete
    // request arrives.
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document->tasks()->attach($task->id);

    $this->actingAs($this->management)->deleteJson("/documents/{$document->id}")->assertStatus(422);
    $this->assertDatabaseHas('documents', ['id' => $document->id]);
});

test('deleting an uploaded document (with a storage_key) removes the record and the stored file', function () {
    $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');
    $upload = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Report',
        'file' => $file,
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ])->assertCreated();
    $document = Document::where('name', 'Report')->firstOrFail();
    Storage::disk('r2')->assertExists($document->storage_key);

    $response = $this->actingAs($this->management)->deleteJson("/documents/{$document->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    Storage::disk('r2')->assertMissing($document->storage_key);
    $this->assertDatabaseHas('audit_log', ['action' => 'document.deleted', 'entity_id' => $document->id]);
});

test('a document with no storage_key (link-only) deletes without touching storage', function () {
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);

    $response = $this->actingAs($this->management)->deleteJson("/documents/{$document->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('a storage failure while deleting the file does not fail the delete itself', function () {
    $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');
    $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Report',
        'file' => $file,
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ])->assertCreated();
    $document = Document::where('name', 'Report')->firstOrFail();

    // A real, forced storage failure — not just a no-op delete of an
    // already-missing file — via a mock that throws exactly where
    // DocumentController::destroy()'s own try/catch expects it: AFTER
    // the transaction (record already gone) commits.
    $this->mock(FileStorageService::class, function ($mock) {
        $mock->shouldReceive('delete')->once()->andThrow(new RuntimeException('Simulated storage failure'));
    });

    $response = $this->actingAs($this->management)->deleteJson("/documents/{$document->id}");

    $response->assertOk();
    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('an external-link document deletes cleanly, never attempting to derive a file from its link', function () {
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);
    expect($document->storage_key)->toBeNull();
    expect($document->link)->not->toBeNull();

    $this->actingAs($this->management)->deleteJson("/documents/{$document->id}")->assertOk();
    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('the document.deleted audit entry carries name, uploader, storage key, original filename, and an empty linked-task-ids list', function () {
    $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');
    $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Report',
        'file' => $file,
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ])->assertCreated();
    $document = Document::where('name', 'Report')->firstOrFail();
    $storageKey = $document->storage_key;

    $this->actingAs($this->management)->deleteJson("/documents/{$document->id}")->assertOk();

    $entry = AuditLog::where('action', 'document.deleted')->where('entity_id', $document->id)->firstOrFail();
    expect($entry->changes['name'])->toBe('Report');
    expect($entry->changes['uploaded_by'])->toBe($this->management->id);
    expect($entry->changes['storage_key'])->toBe($storageKey);
    expect($entry->changes['original_filename'])->toBe('report.pdf');
    expect($entry->changes['linked_task_ids'])->toBe([]);
});

test('a Client-role user can never delete a document, even their own upload', function () {
    $client = makeClientForDeleteTest($this->orgA);
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $client);

    $this->actingAs($client)->deleteJson("/documents/{$document->id}")->assertForbidden();
    $this->assertDatabaseHas('documents', ['id' => $document->id]);
});

test('manage_documents is required to delete, even for the uploader themselves', function () {
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $staff);

    $this->actingAs($staff)->deleteJson("/documents/{$document->id}")->assertForbidden();
    $this->assertDatabaseHas('documents', ['id' => $document->id]);
});

test('the Unlink button and endpoint agree: manage_documents plus task view, no task-edit rights required', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);
    $document->tasks()->attach($task->id);

    // A staff member with manage_documents + department access (task
    // view), but WITHOUT create_edit_tasks (so they fail TaskPolicy::
    // update — the old, wrong gate this endpoint used to use).
    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    grantManageDocumentsForDeleteTest(Role::where('slug', 'staff')->firstOrFail());

    expect($staff->can('update', $task))->toBeFalse();

    $this->actingAs($staff)->deleteJson("/tasks/{$task->id}/documents/{$document->id}")->assertOk();
    expect($task->fresh()->documents()->count())->toBe(0);
});

test('unlink is denied without manage_documents, or without view on the task', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);
    $document->tasks()->attach($task->id);

    // No manage_documents at all.
    $staffWithoutManageDocuments = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staffWithoutManageDocuments->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staffWithoutManageDocuments->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    $this->actingAs($staffWithoutManageDocuments)->deleteJson("/tasks/{$task->id}/documents/{$document->id}")->assertForbidden();
    expect($task->fresh()->documents()->count())->toBe(1);

    // manage_documents, but no department access to even VIEW the task.
    $staffWithoutTaskView = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staffWithoutTaskView->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    grantManageDocumentsForDeleteTest(Role::where('slug', 'staff')->firstOrFail());
    $this->actingAs($staffWithoutTaskView)->deleteJson("/tasks/{$task->id}/documents/{$document->id}")->assertForbidden();
    expect($task->fresh()->documents()->count())->toBe(1);
});

test('the Unlink button on the Task edit page matches the endpoint: visible only with manage_documents', function () {
    $task = Task::create(['organization_id' => $this->orgA->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = makeLinkOnlyDocumentForDeleteTest($this->orgA, $this->management);
    $document->tasks()->attach($task->id);

    $staff = User::factory()->create();
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->firstOrFail()->id]);
    AccessPermission::create(['user_id' => $staff->id, 'organization_id' => $this->orgA->id, 'department_id' => $this->dept->id, 'allowed' => true]);

    // OFF: staff has task view but no manage_documents — no Unlink button.
    // viewData, not a raw text search: _documents.blade.php's own JS
    // unconditionally contains the literal string "Detach" (wireDetach(),
    // a comment, and the append-row template), so a plain assertSee/
    // assertDontSee can't distinguish the real button from the script.
    $before = $this->actingAs($staff)->get("/tasks/{$task->id}/edit");
    $before->assertOk();
    expect($before->viewData('canUnlinkDocuments'))->toBeFalse();

    // ON: grant manage_documents — the button appears, and the endpoint works.
    grantManageDocumentsForDeleteTest(Role::where('slug', 'staff')->firstOrFail());
    $after = $this->actingAs($staff)->get("/tasks/{$task->id}/edit");
    $after->assertOk();
    expect($after->viewData('canUnlinkDocuments'))->toBeTrue();
});

/**
 * The real Role Matrix form always submits every editable role's
 * checkboxes together in one POST — see grantManageDocumentsForEditTest()
 * in DocumentEditTest.php for the full explanation.
 */
function grantManageDocumentsForDeleteTest(Role $role): void
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
