<?php

use App\Enums\FileCategory;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * The image button in the shared rich-text editor (task #4 phase 3) uploads
 * through RichTextImageController, backed by Phase 1's FileStorageService —
 * these tests don't re-check that service's own size/type validation (see
 * tests/Feature/FileStorage/FileStorageServiceTest.php), only that the two
 * upload endpoints reuse it correctly and enforce the right permission for
 * whichever field the image is being attached to.
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

function fakeImage(?int $kilobytes = null): UploadedFile
{
    return UploadedFile::fake()->image('screenshot.png', 200, 200)->size($kilobytes ?? 100);
}

function makeClientWithProjectAccess(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);
    $project->clients()->attach($client->id);

    return $client;
}

// ---------------------------------------------------------------------------
// Existing task: /tasks/{task}/images
// ---------------------------------------------------------------------------

test('a valid image upload for a task description succeeds, returns a URL, and that URL embeds correctly in the saved content', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakeImage(),
        'context' => 'description',
    ]);

    $response->assertCreated()->assertJsonStructure(['url']);
    $url = $response->json('url');
    // Storage::fake() itself always returns a relative "/storage/..." URL
    // regardless of the real disk's own config — a real R2/MinIO URL is
    // absolute (checked manually, see the PR description), but the
    // path shape below is what's actually under this service's control.
    expect($url)->toContain("tasks/{$this->task->id}/images/")->not->toBeEmpty();

    $html = "<p>Before</p><img src=\"{$url}\" alt=\"a screenshot\">";
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => $this->task->title,
        'description' => $html,
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    // Not an exact match against the submitted string: the sanitizer's DOM
    // round trip serializes the void <img> with a self-closing "/>".
    $stored = $this->task->fresh()->description;
    expect($stored)->toContain('<p>Before</p>')->toContain($url)->toContain('alt="a screenshot"');

    // Reload: the same URL renders, in both the editor and the read-only drilldown.
    $editPage = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect(richTextEditorContent($editPage, 'Description'))->toContain($url);

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()->assertSee($url, false);
});

test('an oversized image upload is rejected using FileStorageService\'s own validation, and nothing is stored', function () {
    $oversizeKb = intdiv(FileCategory::Image->config()['max_size'], 1024) + 500;

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakeImage($oversizeKb),
        'context' => 'description',
    ]);

    $response->assertStatus(422)->assertJsonPath('message', fn ($message) => str_contains($message, 'Image files must be'));
    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
});

test('a disallowed file type is rejected the same way', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => UploadedFile::fake()->create('not-an-image.pdf', 100, 'application/pdf'),
        'context' => 'description',
    ]);

    $response->assertStatus(422)->assertJsonPath('message', fn ($message) => str_contains($message, 'not an allowed file type'));
    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
});

test('a user without edit permission on the task cannot upload an image to its description, even via a direct request', function () {
    $client = makeClientWithProjectAccess($this->org, $this->project);

    // Sanity: this user genuinely can view the task (comment-permitted),
    // just not edit its description — otherwise this test would prove nothing.
    $this->actingAs($client)->get("/tasks/{$this->task->id}/edit")->assertOk();

    $this->actingAs($client)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakeImage(),
        'context' => 'description',
    ])->assertForbidden();

    expect(Storage::disk('r2')->allFiles())->toBeEmpty();
});

test('a user who can comment but cannot edit the description can still upload an image within their own comment', function () {
    $client = makeClientWithProjectAccess($this->org, $this->project);

    $response = $this->actingAs($client)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakeImage(),
        'context' => 'comment',
    ]);

    $response->assertCreated();
    $url = $response->json('url');

    $comment = $this->actingAs($client)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => "<p>See attached</p><img src=\"{$url}\">",
    ]);
    $comment->assertCreated();
    expect(Comment::firstOrFail()->body)->toContain($url);
});

test('the comment-image endpoint still requires being able to view the task at all', function () {
    $outsider = User::factory()->create(); // no membership in this org whatsoever

    // Not 403: BelongsToOrganization's global scope means the task's own
    // route-model binding can't even find the row for someone with zero
    // visible organizations — same as every other /tasks/{task}/... route.
    $this->actingAs($outsider)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakeImage(),
        'context' => 'comment',
    ])->assertNotFound();
});

test('an unrecognized context value is rejected as a validation error', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/images", [
        'file' => fakeImage(),
        'context' => 'something-else',
    ])->assertUnprocessable()->assertJsonValidationErrors('context');
});

// ---------------------------------------------------------------------------
// Drafting a new task: /pending-task-images + reconciliation on save
// ---------------------------------------------------------------------------

test('an image uploaded while drafting a new task is stored under a pending path, authorized like creating the task itself', function () {
    $pendingId = (string) Str::uuid();

    $response = $this->actingAs($this->management)->postJson('/pending-task-images', [
        'file' => fakeImage(),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
    ]);

    $response->assertCreated();
    expect($response->json('url'))->toContain("tasks/pending/{$pendingId}/images/");

    // A client can never create a task in this org, so the same rule blocks
    // a pending-image upload too — it isn't a separate, looser permission.
    $client = makeClientWithProjectAccess($this->org, $this->project);
    $this->actingAs($client)->postJson('/pending-task-images', [
        'file' => fakeImage(),
        'project_id' => $this->project->id,
        'pending_id' => (string) Str::uuid(),
    ])->assertForbidden();
});

test('a malformed pending id is rejected as a validation error before it ever reaches storage', function () {
    $this->actingAs($this->management)->postJson('/pending-task-images', [
        'file' => fakeImage(),
        'project_id' => $this->project->id,
        'pending_id' => 'not-a-uuid',
    ])->assertUnprocessable()->assertJsonValidationErrors('pending_id');
});

test('an image uploaded on the Add Task page moves to the real task path once the task is saved, and the embedded URL is rewritten to match', function () {
    $pendingId = (string) Str::uuid();

    $uploaded = $this->actingAs($this->management)->postJson('/pending-task-images', [
        'file' => fakeImage(),
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'pending_id' => $pendingId,
    ])->assertCreated();
    $pendingUrl = $uploaded->json('url');
    expect($pendingUrl)->toContain('tasks/pending/');

    $response = $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Drafted with an image',
        'description' => "<p>Look</p><img src=\"{$pendingUrl}\">",
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $response->assertRedirect();

    $task = Task::where('title', 'Drafted with an image')->firstOrFail();

    expect($task->description)
        ->not->toContain($pendingUrl)
        ->not->toContain('tasks/pending/')
        ->toContain("tasks/{$task->id}/images/");

    // Moved, not copied — the pending copy no longer exists.
    $pendingPath = "tasks/pending/{$pendingId}/images/".basename(parse_url($pendingUrl, PHP_URL_PATH));
    Storage::disk('r2')->assertMissing($pendingPath);

    // The rewritten URL actually renders on reload.
    $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")->assertOk()
        ->assertSee("tasks/{$task->id}/images/", false);
});

test('creating a task with no pending image in its description is unaffected — no reconciliation work, no error', function () {
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
});

test('an abandoned pending image (never referenced by a saved task) is left alone by saving an unrelated task', function () {
    $pendingId = (string) Str::uuid();
    $uploaded = $this->actingAs($this->management)->postJson('/pending-task-images', [
        'file' => fakeImage(),
        'project_id' => $this->project->id,
        'pending_id' => $pendingId,
    ])->assertCreated();

    $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Unrelated task',
        'description' => '<p>No image here</p>',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    // Still sitting under tasks/pending/ — untouched, exactly what
    // images:cleanup-stale-pending exists to eventually sweep up.
    $path = "tasks/pending/{$pendingId}/images/".basename(parse_url($uploaded->json('url'), PHP_URL_PATH));
    Storage::disk('r2')->assertExists($path);
});
