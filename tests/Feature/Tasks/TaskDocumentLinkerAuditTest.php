<?php

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Notifications\AuditEventDatabaseNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * task #73 phase 3: every user-driven task_documents write goes through
 * TaskDocumentLinker now, except Import (already audited by its own batch
 * entries — see the dedicated test at the bottom). This covers the four
 * paths NOT already exercised by TaskDocumentAttachTest's own picker
 * attach()/detach() audit assertions: chip reconciliation on task
 * creation, the inline upload/add-link widget, and smart-link auto-attach.
 */
beforeEach(function () {
    Storage::fake('r2');
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
});

function assertTaskDocumentAttachedAuditEntry(Task $task, Document $document): void
{
    $entry = AuditLog::where('action', 'task.document_attached')
        ->where('entity_type', 'task')
        ->where('entity_id', $task->id)
        ->first();

    expect($entry)->not->toBeNull();
    expect($entry->changes)->toBe(['document_id' => $document->id, 'document_name' => $document->name]);
}

test('chip reconciliation on task creation writes a task.document_attached audit entry', function () {
    $pendingId = (string) Str::uuid();
    $uploaded = $this->actingAs($this->management)->postJson('/pending-task-document-uploads', [
        'file' => UploadedFile::fake()->create('onboarding.pdf', 100, 'application/pdf'),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
    ])->assertCreated();
    $pendingUrl = $uploaded->json('url');

    $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Drafted with a document',
        'description' => "<p>See attached <file-chip href=\"{$pendingUrl}\">onboarding.pdf</file-chip></p>",
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    $task = Task::where('title', 'Drafted with a document')->firstOrFail();
    $document = Document::where('origin_task_id', $task->id)->firstOrFail();

    assertTaskDocumentAttachedAuditEntry($task, $document);
});

test('the inline upload widget writes a task.document_attached audit entry', function () {
    $task = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);

    $this->actingAs($this->management)->post('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Uploaded.pdf',
        'file' => UploadedFile::fake()->create('uploaded.pdf', 100, 'application/pdf'),
        'access_level' => 'internal',
        'task_id' => $task->id,
    ])->assertRedirect();

    $document = Document::where('name', 'Uploaded.pdf')->firstOrFail();

    assertTaskDocumentAttachedAuditEntry($task, $document);
});

test('the inline add-link widget writes a task.document_attached audit entry', function () {
    $task = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);

    $this->actingAs($this->management)->post('/documents', [
        'organization_id' => $this->org->id,
        'name' => 'Linked.pdf',
        'link' => 'https://example.com/linked.pdf',
        'access_level' => 'internal',
        'task_id' => $task->id,
    ])->assertRedirect();

    $document = Document::where('name', 'Linked.pdf')->firstOrFail();

    assertTaskDocumentAttachedAuditEntry($task, $document);
});

test('smart-link auto-attach writes a task.document_attached audit entry', function () {
    Http::fake(['example.com/*' => Http::response('<html><head><meta property="og:title" content="A Great Article"></head></html>', 200)]);

    $task = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);

    $this->actingAs($this->management)->postJson("/tasks/{$task->id}/link-previews", [
        'url' => 'https://example.com/article',
        'context' => 'description',
    ])->assertOk();

    $document = Document::where('link', 'https://example.com/article')->firstOrFail();

    assertTaskDocumentAttachedAuditEntry($task, $document);
});

test('a bulk import does not write a task.document_attached entry — it stays audited only by its own batch entries', function () {
    // Mirrors ImportCommitService::commitTaskDocuments()'s own direct
    // syncWithoutDetaching() call (deliberately not routed through
    // TaskDocumentLinker) — the regression this guards is that call site
    // ever getting migrated onto the linker by mistake, which would
    // double up the audit trail with both document.created (Import's own)
    // and task.document_attached for the same row.
    $task = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = Document::create(['organization_id' => $this->org->id, 'uploaded_by' => $this->management->id, 'name' => 'Imported.pdf', 'link' => 'https://example.com/imported.pdf', 'access_level' => 'internal']);
    $task->documents()->syncWithoutDetaching([$document->id]);

    expect(AuditLog::where('action', 'task.document_attached')->where('entity_id', $task->id)->exists())->toBeFalse();
});

test('a task.document_attached entry appears on the Audit Trail page and triggers no notification', function () {
    $task = Task::create(['organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id, 'title' => 'T', 'priority' => 'medium', 'status' => 'pending']);
    $document = Document::create(['organization_id' => $this->org->id, 'uploaded_by' => $this->management->id, 'name' => 'Vendor Agreement.pdf', 'link' => 'https://example.com/doc.pdf', 'access_level' => 'internal']);

    $this->actingAs($this->management)->postJson("/tasks/{$task->id}/documents", ['document_id' => $document->id])->assertOk();

    $response = $this->actingAs($this->owner)->get('/audit-trail');
    $response->assertOk();
    $response->assertSee('Task Document Attached');
    $response->assertSee('Vendor Agreement.pdf');

    $this->assertDatabaseMissing('notifications', ['type' => AuditEventDatabaseNotification::class]);
});
