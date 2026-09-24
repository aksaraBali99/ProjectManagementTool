<?php

use App\Models\Comment;
use App\Models\Department;
use App\Models\Document;
use App\Models\LinkPreview;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Smart Links (task #4), Tier 1 — a bare URL pasted alone on its own line
 * in the editor calls LinkPreviewController, which resolves it via
 * LinkPreviewService (SSRF-guarded fetch + Open Graph parsing, cached in
 * link_previews). The actual client-side paste detection and node
 * insertion (link-preview-extension.js, buildLinkPreview() in
 * rich-text-editor.js) is pure JS Pest can't execute — verified separately
 * via manual browser testing (see PR description). These tests cover the
 * server side: what gets resolved, what gets blocked, and what gets
 * cached — the same permission model as
 * RichTextImageController/RichTextAudioController/RichTextVideoController/
 * RichTextDocumentController's two actions, reused as-is.
 *
 * Google Docs/Sheets/Slides (Tier 2, same task) is a separate follow-up —
 * not covered here yet.
 */
beforeEach(function () {
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

function makeClientWithProjectAccessForLinkPreview(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);
    $project->clients()->attach($client->id);

    return $client;
}

test('a URL with real Open Graph metadata resolves to a preview with the correct fetched title, image and domain', function () {
    Http::fake([
        'example.com/*' => Http::response(
            '<html><head><meta property="og:title" content="A Great Article"><meta property="og:image" content="https://example.com/cover.png"></head></html>',
            200
        ),
    ]);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/article',
        'context' => 'description',
    ]);

    $response->assertOk()->assertJson([
        'available' => true,
        'url' => 'https://example.com/article',
        'title' => 'A Great Article',
        'image' => 'https://example.com/cover.png',
        'domain' => 'example.com',
    ]);

    expect(LinkPreview::where('url', 'https://example.com/article')->firstOrFail())
        ->title->toBe('A Great Article')
        ->domain->toBe('example.com');
});

test('a page with no og:title falls back to the plain <title> tag', function () {
    Http::fake([
        'example.com/*' => Http::response('<html><head><title>Plain Title</title></head></html>', 200),
    ]);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/plain',
        'context' => 'description',
    ]);

    $response->assertOk()->assertJson(['available' => true, 'title' => 'Plain Title']);
});

test('a URL that fails to fetch (404) falls back to unavailable, never a broken preview', function () {
    Http::fake(['example.com/*' => Http::response('', 404)]);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/missing',
        'context' => 'description',
    ]);

    $response->assertOk()->assertJson(['available' => false]);
    expect(LinkPreview::where('url', 'https://example.com/missing')->first())
        ->title->toBeNull(); // still cached, as a remembered "nothing here"
});

test('a page with a successful response but no usable title falls back to unavailable', function () {
    Http::fake(['example.com/*' => Http::response('<html><head></head><body>no title anywhere</body></html>', 200)]);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/no-title',
        'context' => 'description',
    ])->assertOk()->assertJson(['available' => false]);
});

test('a fetch that throws (connection failure/timeout) falls back to unavailable, not an error response', function () {
    Http::fake(function () {
        throw new ConnectionException('Connection timed out');
    });

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/slow',
        'context' => 'description',
    ])->assertOk()->assertJson(['available' => false]);
});

test('a URL resolving to a private/internal IP is rejected by the SSRF guard and never fetched', function () {
    Http::fake(); // any request reaching Http:: at all fails this test

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'http://127.0.0.1/secret',
        'context' => 'description',
    ]);

    $response->assertOk()->assertJson(['available' => false]);
    Http::assertNothingSent();
});

test('a URL resolving to a link-local/metadata-endpoint IP is also rejected', function () {
    Http::fake();

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'http://169.254.169.254/latest/meta-data/',
        'context' => 'description',
    ])->assertOk()->assertJson(['available' => false]);

    Http::assertNothingSent();
});

test('a non-http(s) scheme is rejected outright', function () {
    Http::fake();

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'file:///etc/passwd',
        'context' => 'description',
    ])->assertOk()->assertJson(['available' => false]);

    Http::assertNothingSent();
});

test('a second paste of the same URL uses the cached entry rather than re-fetching', function () {
    Http::fake([
        'example.com/*' => Http::response('<html><head><meta property="og:title" content="Cached Title"></head></html>', 200),
    ]);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/repeat',
        'context' => 'description',
    ])->assertOk()->assertJson(['title' => 'Cached Title']);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/repeat',
        'context' => 'description',
    ])->assertOk()->assertJson(['title' => 'Cached Title']);

    Http::assertSentCount(1);
});

test('a different user viewing/pasting an already-cached link sees the same cached title, not a live re-fetch using their own credentials', function () {
    // One fake response for the whole test, deliberately never swapped —
    // if the second (different-user) call triggered its own live fetch,
    // it would still get this exact same title back, making a "did it
    // refetch" bug invisible to a title-only assertion. The request-count
    // assertion below is what actually catches that: a second real fetch
    // would push the count to 2, not 1, regardless of what title it got.
    Http::fake([
        'example.com/*' => Http::response('<html><head><meta property="og:title" content="First Fetcher\'s Title"></head></html>', 200),
    ]);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/shared-link',
        'context' => 'description',
    ])->assertOk()->assertJson(['title' => 'First Fetcher\'s Title']);

    $otherManager = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $otherManager->id, 'role_id' => Role::where('slug', 'management')->first()->id]);

    $this->actingAs($otherManager)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/shared-link',
        'context' => 'description',
    ])->assertOk()->assertJson(['title' => 'First Fetcher\'s Title']);

    Http::assertSentCount(1);
});

test('a cache entry older than 7 days is treated as stale and refreshed on the next paste', function () {
    // A single fake() registration, not two — Http::fake() called twice
    // for the SAME URL pattern doesn't replace the earlier stub, it
    // appends one (Factory::fake() merges into stubCallbacks rather than
    // clearing it), so the first-registered response would keep winning
    // for both requests. A call-counting closure is what actually lets
    // the two requests in this test get two different responses.
    $calls = 0;
    Http::fake(function () use (&$calls) {
        $calls++;

        return $calls === 1
            ? Http::response('<html><head><meta property="og:title" content="Old Title"></head></html>', 200)
            : Http::response('<html><head><meta property="og:title" content="Refreshed Title"></head></html>', 200);
    });

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/stale',
        'context' => 'description',
    ])->assertOk()->assertJson(['title' => 'Old Title']);

    LinkPreview::where('url', 'https://example.com/stale')->update(['fetched_at' => now()->subDays(8)]);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/stale',
        'context' => 'description',
    ])->assertOk()->assertJson(['title' => 'Refreshed Title']);

    Http::assertSentCount(2);
});

test('a user without edit permission on the task cannot resolve a link preview for its description, even via a direct request', function () {
    $client = makeClientWithProjectAccessForLinkPreview($this->org, $this->project);
    Http::fake();

    $this->actingAs($client)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/x',
        'context' => 'description',
    ])->assertForbidden();

    Http::assertNothingSent();
});

test('a user who can comment but cannot edit the description can still resolve a link preview within their own comment', function () {
    $client = makeClientWithProjectAccessForLinkPreview($this->org, $this->project);
    Http::fake(['example.com/*' => Http::response('<html><head><meta property="og:title" content="Client Link"></head></html>', 200)]);

    $response = $this->actingAs($client)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/client-link',
        'context' => 'comment',
    ]);
    $response->assertOk()->assertJson(['available' => true, 'title' => 'Client Link']);

    $comment = $this->actingAs($client)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p>See <link-preview href="https://example.com/client-link" title="Client Link" domain="example.com" image=""></link-preview></p>',
    ]);
    $comment->assertCreated();
    expect(Comment::firstOrFail()->body)->toContain('<link-preview');
});

test('the comment-link-preview endpoint still requires being able to view the task at all', function () {
    $outsider = User::factory()->create();
    Http::fake();

    $this->actingAs($outsider)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/x',
        'context' => 'comment',
    ])->assertNotFound();
});

test('an unrecognized context value is rejected as a validation error', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/link-previews", [
        'url' => 'https://example.com/x',
        'context' => 'something-else',
    ])->assertUnprocessable()->assertJsonValidationErrors('context');
});

test('a link preview resolved while drafting a new task is authorized like creating the task itself', function () {
    Http::fake(['example.com/*' => Http::response('<html><head><meta property="og:title" content="Draft Link"></head></html>', 200)]);

    $response = $this->actingAs($this->management)->postJson('/pending-task-link-previews', [
        'url' => 'https://example.com/draft',
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
    ]);
    $response->assertOk()->assertJson(['available' => true, 'title' => 'Draft Link']);

    // A client can never create a task in this org, so the same rule
    // blocks a pending link-preview resolution too.
    $client = makeClientWithProjectAccessForLinkPreview($this->org, $this->project);
    $this->actingAs($client)->postJson('/pending-task-link-previews', [
        'url' => 'https://example.com/draft-2',
        'project_id' => $this->project->id,
    ])->assertForbidden();
});

test('a link-preview-only description round-trips correctly and is not treated as blank', function () {
    $html = '<p><link-preview href="https://example.com/x" title="X" domain="example.com" image="https://example.com/x.png"></link-preview></p>';

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
        ->not->toBeNull()
        ->toContain('<link-preview')
        ->toContain('title="X"')
        ->toContain('domain="example.com"')
        ->toContain('image="https://example.com/x.png"');

    $editPage = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect(descriptionViewContent($editPage))->toContain('<link-preview');
});

test('a link-preview node does not create a Document record or a task_documents attachment — unlike the file-upload document-embedding feature', function () {
    $html = '<p><link-preview href="https://example.com/x" title="X" domain="example.com" image=""></link-preview></p>';

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => $this->task->title,
        'description' => $html,
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    expect(Document::count())->toBe(0);
    expect($this->task->fresh()->documents)->toBeEmpty();
});
