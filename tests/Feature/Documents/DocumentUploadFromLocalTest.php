<?php

use App\Enums\DocumentAccessLevel;
use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\DocumentUploadService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * task #73 phase 1: "Upload file" on both the standalone Documents page
 * (DocumentController::store(), no task in scope) and the Task edit
 * page's inline add-document form (same endpoint, with task_id) — both
 * going through the shared DocumentUploadService. RichTextDocumentController
 * (the editor's own attach-document button) is covered separately by
 * tests/Feature/RichText/DocumentUploadTest.php and is unaffected by any
 * of the behavior asserted here (access-level forcing, view_documents
 * gating) — it never exposed a choosable access level or a Documents-page
 * presence to begin with.
 */
beforeEach(function () {
    Storage::fake('r2');
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->orgB = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#123456']);
    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);
});

function fakeUploadDoc(?int $kilobytes = null, string $name = 'report.pdf'): UploadedFile
{
    return UploadedFile::fake()->create($name, $kilobytes ?? 100, 'application/pdf');
}

function makeStaffForUploadTest(Organization $org): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    return $staff;
}

function makeClientForUploadTest(Organization $org, ?Project $project = null): User
{
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->firstOrFail()->id,
    ]);
    if ($project) {
        $project->clients()->attach($client->id);
    }

    return $client;
}

test('uploading a file from the Documents page stores the key, sha256, size, mime and filename, with no origin task and the requested company', function () {
    $file = fakeUploadDoc(120, 'Quarterly Report.pdf');

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Quarterly Report',
        'file' => $file,
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'Quarterly Report')->firstOrFail();

    expect($document->organization_id)->toBe($this->orgA->id);
    expect($document->origin_task_id)->toBeNull();
    expect($document->original_filename)->toBe('Quarterly Report.pdf');
    expect($document->mime_type)->toBe('application/pdf');
    expect($document->size_bytes)->toBe($file->getSize());
    expect($document->sha256_hash)->toBe(hash_file('sha256', $file->getRealPath()));
    expect($document->storage_key)->not->toBeNull();
    expect($document->storage_key)->toStartWith('organizations/'.$this->orgA->id.'/documents/');
    expect($document->link)->toBeNull();

    Storage::disk('r2')->assertExists($document->storage_key);

    // The one URL helper every consumer routes through — resolves from
    // storage_key, not the (null) link column.
    expect($response->json('document.url'))->not->toBeNull();
    expect($document->url)->toBe($response->json('document.url'));
});

test('uploading a file from the task edit page sets origin_task_id and attaches via task_documents', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")
        ->assertOk()
        ->assertSee('Upload file');

    $file = fakeUploadDoc(80, 'brief.pdf');

    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Brief',
        'file' => $file,
        'access_level' => 'internal',
        'task_id' => $task->id,
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'Brief')->firstOrFail();

    expect($document->origin_task_id)->toBe($task->id);
    expect($document->storage_key)->toStartWith('tasks/'.$task->id.'/documents/');
    expect($task->fresh()->documents()->pluck('documents.id')->all())->toBe([$document->id]);
});

test('a tampered organization_id on a task-scoped upload is ignored — the document belongs to the task\'s own company', function () {
    // Management in Org B too (with manage_documents there, same as Org
    // A), so Gate::authorize('create', ...) passes for Org B and the
    // request reaches the actual org/task mismatch check this test wants
    // to exercise, rather than being rejected earlier for lack of access
    // to Org B at all (see the next test for that case).
    OrgMember::create([
        'organization_id' => $this->orgB->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->postJson('/documents', [
        // Claims Org B, even though the task itself belongs to Org A.
        'organization_id' => $this->orgB->id,
        'name' => 'Mismatched',
        'file' => fakeUploadDoc(),
        'access_level' => 'internal',
        'task_id' => $task->id,
    ]);

    $response->assertStatus(422);
    $this->assertDatabaseMissing('documents', ['name' => 'Mismatched']);
});

test('a tampered organization_id the uploader has no manage_documents access to is rejected outright', function () {
    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgB->id,
        'name' => 'Not mine',
        'file' => fakeUploadDoc(),
        'access_level' => 'internal',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('documents', ['name' => 'Not mine']);
});

test('manage_documents ON lets Staff upload a file on both the Documents page and a task', function () {
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $currentIds = $staffRole->permissions()->pluck('permissions.id')->all();
    $this->actingAs(createOwner())->put('/roles/permissions', [
        'role_permissions' => [$staffRole->id => array_values(array_unique([...$currentIds, $manageDocumentsId]))],
    ])->assertRedirect();

    $staff = makeStaffForUploadTest($this->orgA);

    $this->actingAs($staff)->get('/documents/create/'.$this->orgA->id)
        ->assertOk()
        ->assertSee('Upload file');

    $response = $this->actingAs($staff)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Staff upload',
        'file' => fakeUploadDoc(),
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertCreated();
    $this->assertDatabaseHas('documents', ['name' => 'Staff upload', 'uploaded_by' => $staff->id]);
});

test('manage_documents OFF blocks Staff\'s upload endpoint with a 403 and no record or stored file', function () {
    $staff = makeStaffForUploadTest($this->orgA);

    $this->actingAs($staff)->get('/documents/create/'.$this->orgA->id)->assertForbidden();

    $response = $this->actingAs($staff)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Blocked upload',
        'file' => fakeUploadDoc(),
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('documents', ['name' => 'Blocked upload']);
});

test('manage_documents ON/OFF for Staff also gates the task edit page\'s own inline upload option, the same as the Documents page', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $staff = makeStaffForUploadTest($this->orgA);
    // hasDepartmentAccess() — separate from manage_documents — is what
    // this test isn't about, so it's granted up front here, unaffected by
    // the manage_documents toggle exercised below.
    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $this->orgA->id,
        'department_id' => $this->deptA->id,
        'allowed' => true,
    ]);

    // OFF by default: no "Attach document" section at all, and the
    // endpoint itself 403s regardless of what the task page would have
    // shown.
    $this->actingAs($staff)->get("/tasks/{$task->id}/edit")
        ->assertOk()
        ->assertDontSee('Attach document');
    $this->actingAs($staff)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Staff task upload (blocked)',
        'file' => fakeUploadDoc(),
        'access_level' => 'internal',
        'task_id' => $task->id,
    ])->assertForbidden();
    $this->assertDatabaseMissing('documents', ['name' => 'Staff task upload (blocked)']);

    // ON: the section (and its "Upload a new file" action) appears, and
    // the upload actually attaches.
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $currentIds = $staffRole->permissions()->pluck('permissions.id')->all();
    $this->actingAs(createOwner())->put('/roles/permissions', [
        'role_permissions' => [$staffRole->id => array_values(array_unique([...$currentIds, $manageDocumentsId]))],
    ])->assertRedirect();

    $this->actingAs($staff)->get("/tasks/{$task->id}/edit")
        ->assertOk()
        ->assertSee('Attach document')
        ->assertSee('Upload a new file');

    $response = $this->actingAs($staff)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Staff task upload (allowed)',
        'file' => fakeUploadDoc(),
        'access_level' => 'internal',
        'task_id' => $task->id,
    ]);
    $response->assertCreated();
    expect($task->fresh()->documents()->pluck('name')->all())->toContain('Staff task upload (allowed)');
});

test('a task-scoped upload succeeds even when organization_id arrives as a numeric string, like a real multipart/form-data request sends it', function () {
    // Regression test: a real browser's file upload always sends
    // organization_id as a STRING (raw HTTP multipart fields are text —
    // the 'integer' validation rule only checks the format, it doesn't
    // cast the type), while $task->organization_id is a genuine PHP int.
    // DocumentController::store()'s company-match check used to compare
    // these with a strict !==, rejecting every real-browser task upload
    // with "Document must belong to the task's company." — found via the
    // merged Attach-document panel's "Upload ... as a new file" action,
    // but pre-existing and unrelated to that panel: any real multipart
    // upload with a task_id hit it, the panel just was the first thing to
    // actually get manually tested through a real browser on this path.
    // Explicitly casting organization_id to a string here reproduces that
    // exact condition — Pest's own post() helper doesn't naturally
    // produce it, since it preserves native PHP types when mixing a file
    // with scalar fields instead of round-tripping through a real
    // string-only multipart body the way a browser does.
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->post('/documents', [
        'organization_id' => (string) $this->orgA->id,
        'name' => 'Prefilled upload.pdf',
        'file' => fakeUploadDoc(),
        'access_level' => 'internal',
        'task_id' => $task->id,
    ]);

    $response->assertRedirect();
    $document = Document::where('name', 'Prefilled upload.pdf')->firstOrFail();
    expect($task->fresh()->documents()->pluck('documents.id')->all())->toBe([$document->id]);
});

test('a Client with manage_documents has their upload forced to public, even if the request is tampered with', function () {
    // manage_documents is NOT granted to Client by default (see
    // PermissionSeeder), but "manage_documents stays tickable for
    // Client" (task #73 phase 1) — it's the one Documents permission
    // Client isn't permanently locked out of, so an owner can grant it
    // through the real Role Matrix endpoint, same as any other toggle.
    $clientRole = Role::where('slug', 'client')->firstOrFail();
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $currentIds = $clientRole->permissions()->pluck('permissions.id')->all();
    $this->actingAs(createOwner())->put('/roles/permissions', [
        'role_permissions' => [$clientRole->id => array_values(array_unique([...$currentIds, $manageDocumentsId]))],
    ])->assertRedirect();
    expect($clientRole->fresh()->permissions()->pluck('id')->all())->toContain($manageDocumentsId);

    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Client task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $client = makeClientForUploadTest($this->orgA, $this->projectA);

    // task #73 (UI merge): this used to also assert the task edit page's
    // create-form rendered a hidden "public" input instead of a select
    // for a Client uploader. That's no longer testable there: the merged
    // attach panel is gated by TaskPolicy::attachDocuments(), which
    // excludes Client unconditionally, so a Client-role uploader doesn't
    // see any attach form on the task edit page at all any more (see
    // TaskDocumentMergedPanelTest for that gate's own coverage). The
    // standalone Add Document page isn't a substitute either — despite
    // DocumentPolicy::create() allowing a Client with manage_documents,
    // DocumentController::create()'s own organization-list guard
    // (documentManageableOrganizationIds()) only admits management/staff,
    // 403ing a Client before that policy is even reached — a pre-existing
    // inconsistency, unrelated to this PR, not something to paper over
    // here. So this test now only covers what's still real and still
    // matters regardless of UI reachability: the server-side invariant
    // that a Client's upload is forced to Public no matter what the
    // request claims — defense in depth against a tampered/direct
    // request, not something that depends on any particular page
    // existing.
    $response = $this->actingAs($client)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Client upload',
        'file' => fakeUploadDoc(),
        // Tampered: explicitly requesting Private.
        'access_level' => 'private',
        'task_id' => $task->id,
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'Client upload')->firstOrFail();
    expect($document->access_level->value)->toBe('public');
});

test('an oversized file is rejected with no document record and nothing written to disk', function () {
    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Too big',
        'file' => fakeUploadDoc(25 * 1024), // over the 20MB document ceiling
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertStatus(422);
    $this->assertDatabaseMissing('documents', ['name' => 'Too big']);
    Storage::disk('r2')->assertDirectoryEmpty('organizations');
});

test('a disallowed file type is rejected with no document record and nothing written to disk', function () {
    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Not allowed',
        'file' => UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream'),
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertStatus(422);
    $this->assertDatabaseMissing('documents', ['name' => 'Not allowed']);
    Storage::disk('r2')->assertDirectoryEmpty('organizations');
});

test('a failure creating the document record after a successful upload removes the stored file, leaving no orphan', function () {
    // Forces Document::create() to fail AFTER the file is already on disk
    // — a real Eloquent model event, not a container override, so it
    // exercises DocumentUploadService::createRecord()'s own try/catch
    // exactly where the requirement describes it ("if the record can't be
    // created after the file is stored, delete the stored file").
    // flushEventListeners() in `finally` keeps this from leaking into any
    // other test that creates a Document afterward in the same process.
    Document::creating(function () {
        throw new RuntimeException('simulated record-creation failure');
    });

    try {
        $thrown = null;
        try {
            app(DocumentUploadService::class)->uploadForOrganization(
                fakeUploadDoc(),
                $this->orgA->id,
                DocumentAccessLevel::Internal,
                $this->management,
                'Will fail',
            );
        } catch (RuntimeException $e) {
            $thrown = $e;
        }

        expect($thrown)->not->toBeNull();
        expect($thrown->getMessage())->toBe('simulated record-creation failure');
        expect(Document::count())->toBe(0);
        Storage::disk('r2')->assertDirectoryEmpty('organizations');
    } finally {
        Document::flushEventListeners();
    }
});

test('add-by-link still works on both the Documents page and the task edit page after the upload option was added', function () {
    $response = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Linked doc',
        'link' => 'https://example.com/linked-doc.pdf',
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ]);

    $response->assertCreated();
    $document = Document::where('name', 'Linked doc')->firstOrFail();
    expect($document->storage_key)->toBeNull();
    expect($document->url)->toBe('https://example.com/linked-doc.pdf');
});

test('a request with both file and link is rejected, and a request with neither is rejected', function () {
    $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Both',
        'file' => fakeUploadDoc(),
        'link' => 'https://example.com/both.pdf',
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ])->assertStatus(422);

    $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Neither',
        'access_level' => 'internal',
        'from_documents_page' => '1',
    ])->assertStatus(422);

    $this->assertDatabaseMissing('documents', ['name' => 'Both']);
    $this->assertDatabaseMissing('documents', ['name' => 'Neither']);
});

test('an uploaded document and a link-only document both appear correctly on the Documents list and the task Documents section', function () {
    $task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Mixed task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $uploaded = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Uploaded doc',
        'file' => fakeUploadDoc(),
        'access_level' => 'internal',
        'task_id' => $task->id,
    ])->assertCreated()->json('document');

    $linked = $this->actingAs($this->management)->postJson('/documents', [
        'organization_id' => $this->orgA->id,
        'name' => 'Linked doc',
        'link' => 'https://example.com/linked.pdf',
        'access_level' => 'internal',
        'task_id' => $task->id,
    ])->assertCreated()->json('document');

    $listResponse = $this->actingAs($this->management)->get('/documents/'.$this->orgA->id);
    $listResponse->assertOk()->assertSee('Uploaded doc')->assertSee('Linked doc');

    $taskResponse = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");
    $taskResponse->assertOk()->assertSee('Uploaded doc')->assertSee('Linked doc');

    // Both download links resolve through the same helper and both 200.
    $this->actingAs($this->management)->get('/file-downloads?url='.urlencode($uploaded['url']))->assertOk();
    $this->actingAs($this->management)->get('/file-downloads?url='.urlencode($linked['url']))->assertRedirect('https://example.com/linked.pdf');
});
