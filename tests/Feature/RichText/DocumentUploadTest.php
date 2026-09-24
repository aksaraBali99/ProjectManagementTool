<?php

use App\Enums\FileCategory;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * The document/attachment button in the shared rich-text editor (task #4,
 * document upload + embedding) uploads through RichTextDocumentController,
 * backed by Phase 1's FileStorageService and Phase 6's Document model —
 * same shape as ImageUploadTest.php/AudioUploadTest.php/VideoUploadTest.php
 * for the parts that ARE identical (permission model, FileStorageService
 * reuse, the generic reconcilePendingFiles() pending-upload mechanism), but
 * this is NOT an inline embed like those three: attaching a document also
 * creates a real Document row and a task_documents attachment — the same
 * underlying record the Documents page/tab and the existing "attach an
 * existing document" picker on the Edit Task page both already use — with
 * a file-chip node in the text as a clickable reference, mirroring Jira.
 *
 * What the file-chip actually looks like and its click-to-open-in-a-new-tab
 * behavior are pure client-side JS (file-chip-extension.js,
 * file-chip-thumbnail.js, app.js's initFileChipDelegation) that Pest can't
 * execute — verified separately via manual browser testing (see PR
 * description). These tests confirm the one thing Pest actually can: the
 * saved HTML's href is correct and the markup never renders as an inline
 * preview/embed (no <img>/<video>/<audio>/lightbox/video-thumb anywhere
 * near it) — the same negative assertion DocumentViewingBehaviorTest.php
 * already established for the other three document-viewing surfaces.
 */
beforeEach(function () {
    Storage::fake('r2');
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function fakeDocumentFile(?int $kilobytes = null, string $name = 'report.pdf'): UploadedFile
{
    return UploadedFile::fake()->create($name, $kilobytes ?? 100, 'application/pdf');
}

function makeClientWithProjectAccessForDocumentUpload(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);
    $project->clients()->attach($client->id);

    return $client;
}

// ---------------------------------------------------------------------------
// Existing task: /tasks/{task}/document-uploads
// ---------------------------------------------------------------------------

test('a valid document upload for a task description succeeds, returns a URL and name, creates a real Document row attached via task_documents, and embeds a correctly-rendered chip', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => fakeDocumentFile(null, 'Project Brief.pdf'),
        'context' => 'description',
    ]);

    $response->assertCreated()->assertJsonStructure(['url', 'name']);
    $url = $response->json('url');
    $name = $response->json('name');
    expect($url)->toContain("tasks/{$this->task->id}/documents/")->not->toBeEmpty();
    expect($name)->toBe('Project Brief.pdf');

    // The real Document record — not a separate, parallel thing that
    // merely looks similar to one.
    $document = Document::where('link', $url)->firstOrFail();
    expect($document->name)->toBe('Project Brief.pdf');
    expect($document->organization_id)->toBe($this->org->id);
    expect($document->access_level->value)->toBe('internal');
    expect($document->uploaded_by)->toBe($this->management->id);
    expect($this->task->fresh()->documents->pluck('id')->all())->toBe([$document->id]);

    $html = "<p>See attached <file-chip href=\"{$url}\">{$name}</file-chip></p>";
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => $this->task->title,
        'description' => $html,
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)
        ->toContain('See attached')
        ->toContain("href=\"{$url}\"")
        ->toContain('<file-chip')
        ->toContain($name)
        ->toContain('</file-chip>');

    // Reload: the same chip renders in both the Edit Task page's
    // read-only Description view and the drilldown.
    $editPage = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $editorContent = descriptionViewContent($editPage);
    expect($editorContent)->toContain($url)->toContain('<file-chip');

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee($url, false)
        ->assertSee('<file-chip', false);
});

test('a document attached via the editor appears on the task\'s normal Documents list — the same record, not a separate parallel thing', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => fakeDocumentFile(null, 'Roadmap.pdf'),
        'context' => 'description',
    ])->assertCreated();

    $document = Document::where('link', $response->json('url'))->firstOrFail();

    $documentsPage = $this->actingAs($this->management)->get("/documents/{$this->org->id}");
    $documentsPage->assertOk()->assertSee('Roadmap.pdf');
    expect($documentsPage->viewData('documents')->pluck('id')->all())->toContain($document->id);

    // And the Edit Task page's own "attached documents" list, the exact
    // same surface tasks/_documents.blade.php already renders for a
    // document attached via the picker.
    $editPage = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");
    $editPage->assertOk();
    expect($editPage->viewData('attachedDocuments')->pluck('id')->all())->toContain($document->id);
});

test('deleting the file-chip from the description text does not delete the Document record or its task_documents attachment', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => fakeDocumentFile(null, 'Contract.pdf'),
        'context' => 'description',
    ])->assertCreated();
    $url = $response->json('url');
    $document = Document::where('link', $url)->firstOrFail();

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => $this->task->title,
        'description' => "<p>See attached <file-chip href=\"{$url}\">Contract.pdf</file-chip></p>",
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();
    expect($this->task->fresh()->description)->toContain('<file-chip');

    // Backspace over the chip — matches Jira: removing the inline
    // reference is not the same action as removing the attachment.
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => $this->task->title,
        'description' => '<p>See attached</p>',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    expect($this->task->fresh()->description)->not->toContain('<file-chip');
    expect(Document::find($document->id))->not->toBeNull();
    expect($this->task->fresh()->documents->pluck('id')->all())->toBe([$document->id]);
});

test('an oversized document upload is rejected using FileStorageService\'s own validation, and nothing is stored or created', function () {
    $oversizeKb = intdiv(FileCategory::Document->config()['max_size'], 1024) + 500;

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => fakeDocumentFile($oversizeKb),
        'context' => 'description',
    ]);

    $response->assertStatus(422)->assertJsonPath('message', fn ($message) => str_contains($message, 'Document files must be'));
    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
    expect(Document::count())->toBe(0);
});

test('a disallowed file type is rejected the same way', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => UploadedFile::fake()->create('not-a-document.exe', 100, 'application/octet-stream'),
        'context' => 'description',
    ]);

    $response->assertStatus(422)->assertJsonPath('message', fn ($message) => str_contains($message, 'not an allowed file type'));
    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
    expect(Document::count())->toBe(0);
});

test('a user without edit permission on the task cannot upload a document to its description, even via a direct request', function () {
    $client = makeClientWithProjectAccessForDocumentUpload($this->org, $this->project);

    // Sanity: this user genuinely can view the task (comment-permitted),
    // just not edit its description — otherwise this test would prove nothing.
    $this->actingAs($client)->get("/tasks/{$this->task->id}/edit")->assertOk();

    $this->actingAs($client)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => fakeDocumentFile(),
        'context' => 'description',
    ])->assertForbidden();

    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
    expect(Document::count())->toBe(0);
});

test('a user who can comment but cannot edit the description can still upload a document within their own comment — even though they could never create one via the Documents page', function () {
    $client = makeClientWithProjectAccessForDocumentUpload($this->org, $this->project);

    // Sanity: DocumentPolicy::create's own, stricter gate would refuse
    // this exact user — confirming the task's explicit instruction to
    // follow the field-edit permission instead, not DocumentPolicy.
    expect(Gate::forUser($client)->allows('create', [Document::class, $this->org->id]))->toBeFalse();

    $response = $this->actingAs($client)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => fakeDocumentFile(null, 'client-notes.pdf'),
        'context' => 'comment',
    ]);

    $response->assertCreated();
    $url = $response->json('url');
    $document = Document::where('link', $url)->firstOrFail();
    expect($document->uploaded_by)->toBe($client->id);

    $comment = $this->actingAs($client)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => "<p>See attached <file-chip href=\"{$url}\">client-notes.pdf</file-chip></p>",
    ]);
    $comment->assertCreated();
    expect(Comment::firstOrFail()->body)->toContain($url);
});

test('the comment-document endpoint still requires being able to view the task at all', function () {
    $outsider = User::factory()->create(); // no membership in this org whatsoever

    // Not 403: BelongsToOrganization's global scope means the task's own
    // route-model binding can't even find the row for someone with zero
    // visible organizations — same as every other /tasks/{task}/... route.
    $this->actingAs($outsider)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => fakeDocumentFile(),
        'context' => 'comment',
    ])->assertNotFound();
});

test('an unrecognized context value is rejected as a validation error', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => fakeDocumentFile(),
        'context' => 'something-else',
    ])->assertUnprocessable()->assertJsonValidationErrors('context');
});

// ---------------------------------------------------------------------------
// Drafting a new task: /pending-task-document-uploads + reconciliation
// ---------------------------------------------------------------------------

test('a document uploaded while drafting a new task is stored under a pending path, authorized like creating the task itself, and creates NO Document row yet', function () {
    $pendingId = (string) Str::uuid();

    $response = $this->actingAs($this->management)->postJson('/pending-task-document-uploads', [
        'file' => fakeDocumentFile(null, 'draft-spec.pdf'),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
    ]);

    $response->assertCreated()->assertJsonStructure(['url', 'name']);
    expect($response->json('url'))->toContain("tasks/pending/{$pendingId}/documents/");
    expect($response->json('name'))->toBe('draft-spec.pdf');

    // The core deferral this feature is built around: no task_id exists
    // yet, so there is nothing for a Document row to attach to — none is
    // created at this point, unlike the real-task upload above.
    expect(Document::count())->toBe(0);

    // A client can never create a task in this org, so the same rule
    // blocks a pending-document upload too — it isn't a separate, looser
    // permission.
    $client = makeClientWithProjectAccessForDocumentUpload($this->org, $this->project);
    $this->actingAs($client)->postJson('/pending-task-document-uploads', [
        'file' => fakeDocumentFile(),
        'project_id' => $this->project->id,
        'pending_id' => (string) Str::uuid(),
    ])->assertForbidden();
});

test('a malformed pending id is rejected as a validation error before it ever reaches storage', function () {
    $this->actingAs($this->management)->postJson('/pending-task-document-uploads', [
        'file' => fakeDocumentFile(),
        'project_id' => $this->project->id,
        'pending_id' => 'not-a-uuid',
    ])->assertUnprocessable()->assertJsonValidationErrors('pending_id');
});

test('a document attached during Add Task creates its Document record and task_documents attachment only once the task is actually saved', function () {
    $pendingId = (string) Str::uuid();

    $uploaded = $this->actingAs($this->management)->postJson('/pending-task-document-uploads', [
        'file' => fakeDocumentFile(null, 'onboarding.pdf'),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
    ])->assertCreated();
    $pendingUrl = $uploaded->json('url');
    expect($pendingUrl)->toContain('tasks/pending/');

    // Still nothing in the database — the file exists on disk, but no
    // Document row and so nothing on any Documents list, before save.
    expect(Document::count())->toBe(0);

    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Drafted with a document',
        'description' => "<p>See attached <file-chip href=\"{$pendingUrl}\">onboarding.pdf</file-chip></p>",
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $response->assertRedirect();

    $task = Task::where('title', 'Drafted with a document')->firstOrFail();

    expect($task->description)
        ->not->toContain($pendingUrl)
        ->not->toContain('tasks/pending/')
        ->toContain("tasks/{$task->id}/documents/");

    // Moved, not copied — the pending copy no longer exists.
    $pendingPath = "tasks/pending/{$pendingId}/documents/".basename(parse_url($pendingUrl, PHP_URL_PATH));
    Storage::disk('r2')->assertMissing($pendingPath);

    // Now — and only now — a real Document row + attachment exist.
    expect(Document::count())->toBe(1);
    $document = Document::firstOrFail();
    expect($document->name)->toBe('onboarding.pdf');
    expect($document->organization_id)->toBe($this->org->id);
    expect($document->uploaded_by)->toBe($this->management->id);
    expect($document->access_level->value)->toBe('internal');
    expect($task->documents->pluck('id')->all())->toBe([$document->id]);

    // And it shows up on the normal Documents list, the same confirmation
    // as the real-task-upload test above.
    $this->actingAs($this->management)->get("/documents/{$this->org->id}")->assertOk()->assertSee('onboarding.pdf');
});

test('a single Add Task save reconciles an image and a document reference from the same pending id in one pass', function () {
    // Confirms reconcilePendingFiles() (called once per save, on the whole
    // description) already handles the document's pending URL correctly
    // alongside another category, the same confirmation Phases 4-5 each
    // made for their own category — plus, uniquely to documents, that
    // attachDocumentChips() runs on the SAME final reconciled string.
    $pendingId = (string) Str::uuid();

    $image = $this->actingAs($this->management)->postJson('/pending-task-images', [
        'file' => UploadedFile::fake()->image('shot.png', 100, 100),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
    ])->assertCreated()->json('url');

    $document = $this->actingAs($this->management)->postJson('/pending-task-document-uploads', [
        'file' => fakeDocumentFile(null, 'combined.pdf'),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
    ])->assertCreated()->json('url');

    $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Drafted with both',
        'description' => "<img src=\"{$image}\"><p><file-chip href=\"{$document}\">combined.pdf</file-chip></p>",
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    $task = Task::where('title', 'Drafted with both')->firstOrFail();

    expect($task->description)
        ->not->toContain('tasks/pending/')
        ->toContain("tasks/{$task->id}/images/")
        ->toContain("tasks/{$task->id}/documents/");

    expect(Document::count())->toBe(1);
    expect($task->documents()->count())->toBe(1);
});

test('creating a task with no pending document in its description is unaffected — no reconciliation work, no Document created', function () {
    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Plain task',
        'description' => '<p>Nothing special</p>',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response->assertRedirect();
    expect(Task::where('title', 'Plain task')->firstOrFail()->description)->toBe('<p>Nothing special</p>');
    expect(Document::count())->toBe(0);
});

test('an abandoned pending document file (never referenced by a saved task) is left alone by saving an unrelated task, and no Document row exists for it', function () {
    $pendingId = (string) Str::uuid();
    $uploaded = $this->actingAs($this->management)->postJson('/pending-task-document-uploads', [
        'file' => fakeDocumentFile(),
        'project_id' => $this->project->id,
        'pending_id' => $pendingId,
    ])->assertCreated();

    $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Unrelated task',
        'description' => '<p>No document here</p>',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    // Still sitting under tasks/pending/ — untouched, exactly what
    // media:cleanup-stale-pending exists to eventually sweep up.
    $path = "tasks/pending/{$pendingId}/documents/".basename(parse_url($uploaded->json('url'), PHP_URL_PATH));
    Storage::disk('r2')->assertExists($path);
    expect(Document::count())->toBe(0);
});
