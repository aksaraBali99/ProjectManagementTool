<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\FileStorageService;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * A direct link to $document->link always saved a downloaded file under
 * its bare tasks/{id}/documents/{uuid}.ext storage key — the browser has
 * no other name to go on — instead of the file's real original name.
 * /file-downloads (DocumentController::download) is the one place every
 * document link in the app now routes through (documents/index.blade.php,
 * tasks/_documents.blade.php, and a file-chip's click-to-open in app.js)
 * so an uploaded file always downloads under its real name, while an
 * external Document link (Smart Links, a manually-added URL) keeps
 * opening exactly as it always did.
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

function makeClientWithProjectAccessForDownload(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);
    $project->clients()->attach($client->id);

    return $client;
}

function uploadDocumentForDownload(User $actor, Task $task, string $name = 'Project Brief.pdf'): Document
{
    $url = test()->actingAs($actor)->postJson("/tasks/{$task->id}/document-uploads", [
        'file' => UploadedFile::fake()->create($name, 100, 'application/pdf'),
        'context' => 'description',
    ])->assertCreated()->json('url');

    return Document::where('link', $url)->firstOrFail();
}

test('downloading an uploaded document streams it with the real original filename in Content-Disposition, not the storage key', function () {
    $document = uploadDocumentForDownload($this->management, $this->task, 'Project Brief.pdf');

    $response = $this->actingAs($this->management)->get('/file-downloads?url='.urlencode($document->link));

    $response->assertOk();
    // The storage key really is just a uuid — proves the filename below
    // came from the Document record, not simply echoed back from the URL.
    expect($document->link)->not->toContain('Project Brief.pdf');
    expect($response->headers->get('Content-Disposition'))->toContain('Project Brief.pdf');
});

test('downloading an external-link document redirects to the link instead of streaming — there is nothing of ours to serve', function () {
    $document = Document::create([
        'organization_id' => $this->org->id,
        'uploaded_by' => $this->management->id,
        'name' => 'External spec',
        'link' => 'https://docs.google.com/document/d/abc123/edit',
        'access_level' => 'internal',
    ]);

    $response = $this->actingAs($this->management)->get('/file-downloads?url='.urlencode($document->link));

    $response->assertRedirect('https://docs.google.com/document/d/abc123/edit');
});

test('a url with no matching Document record 404s rather than proxying an arbitrary URL', function () {
    $this->actingAs($this->management)
        ->get('/file-downloads?url='.urlencode('https://evil.example.com/steal'))
        ->assertNotFound();
});

test('a client can download a document they themselves attached via a comment on their own project, even though it defaults to internal access and they hold no view_documents permission', function () {
    $client = makeClientWithProjectAccessForDownload($this->org, $this->project);

    $url = $this->actingAs($client)->postJson("/tasks/{$this->task->id}/document-uploads", [
        'file' => UploadedFile::fake()->create('client-notes.pdf', 100, 'application/pdf'),
        'context' => 'comment',
    ])->assertCreated()->json('url');
    $document = Document::where('link', $url)->firstOrFail();
    expect($document->access_level->value)->toBe('internal');

    // Sanity: DocumentPolicy::view alone would refuse this — internal,
    // not public, and clients never hold view_documents. The
    // task-attachment door is what /file-downloads relies on instead.
    expect($client->can('view', $document))->toBeFalse();

    $response = $this->actingAs($client)->get('/file-downloads?url='.urlencode($document->link));
    $response->assertOk();
    expect($response->headers->get('Content-Disposition'))->toContain('client-notes.pdf');
});

test('a client with no access to the project this document is attached to, and no other visibility door, gets a 403', function () {
    $document = uploadDocumentForDownload($this->management, $this->task, 'Internal only.pdf');

    $otherProject = Project::create(['organization_id' => $this->org->id, 'name' => 'Other project', 'description' => 'd']);
    $outsiderClient = makeClientWithProjectAccessForDownload($this->org, $otherProject);

    $this->actingAs($outsiderClient)->get('/file-downloads?url='.urlencode($document->link))
        ->assertForbidden();
});

test('a user with no membership in the document\'s organization at all gets a 404, same as any other tenant-scoped lookup', function () {
    $document = uploadDocumentForDownload($this->management, $this->task);
    $outsider = User::factory()->create();

    $this->actingAs($outsider)->get('/file-downloads?url='.urlencode($document->link))
        ->assertNotFound();
});

test('FileStorageService::download returns null for a URL outside its own disk, and a streamed response with the given filename for one inside it', function () {
    $service = new FileStorageService;

    expect($service->download('https://example.com/not-ours.pdf', 'Whatever.pdf'))->toBeNull();

    $document = uploadDocumentForDownload($this->management, $this->task, 'Real File.pdf');
    $response = $service->download($document->link, 'Real File.pdf');

    expect($response)->not->toBeNull();
    expect($response->headers->get('Content-Disposition'))->toContain('Real File.pdf');
});
