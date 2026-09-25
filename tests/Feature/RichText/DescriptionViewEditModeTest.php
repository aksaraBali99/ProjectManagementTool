<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

/**
 * Task Description's view/edit split (task #4, follow-up fix). Before this,
 * Description was a permanently-live TipTap editor on the Edit Task page,
 * which meant an embedded image could never open the Phase 3 lightbox — a
 * click was always intercepted by the editor for node selection/resize
 * instead. This mirrors the pattern Comments already used (confirmed, not
 * assumed — see tasks/_comments.blade.php): read-only by default, an
 * explicit Edit control, the live editor mounted only once Edit is clicked.
 *
 * Edit mode itself later dropped its Save/Cancel buttons for autosave-on-
 * blur (task #4, description autosave) — entering edit mode has no
 * separate save step and nothing to cancel any more, but the read-only
 * view / Edit button split this file actually tests is unchanged.
 *
 * The actual click-to-edit interaction (Edit -> live editor pre-populated
 * -> autosaves and returns to view once focus leaves the editor) can't be
 * driven from Pest — there's no JS runner in this suite, the same caveat
 * EmojiSupportTest's own shared-component test already documents — so
 * that round trip is covered by a manual browser check instead (see the
 * PR description). These tests pin the structural and server-side
 * guarantees an HTTP response can actually verify.
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
        'description' => '<p>Hello</p><img src="https://cdn.example.com/tasks/1/images/a.jpg" alt="a screenshot">',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function makeClientWithProjectAccessForDescription(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);
    $project->clients()->attach($client->id);

    return $client;
}

test('Description renders as read-only by default on the Edit Task page — no live editor initialized', function () {
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    // No live editor root for Description in the default response — the
    // whole point of this fix. Comments' own "New comment" editor is still
    // always-live, unaffected — this scopes to Description only (Add Task
    // is also unaffected, see the dedicated test further down).
    expect(richTextEditorNode($page, 'Description'))->toBeNull();

    // The read-only view is there instead, with the actual saved content.
    expect(descriptionViewContent($page))->toContain('<p>Hello</p>')->toContain('<img');
});

test('the embedded image in view mode sits inside the exact container the lightbox delegates from', function () {
    // initLightboxDelegation() (app.js) is a single document-level listener
    // scoped to the selector `[data-rich-text-content] img` — confirming
    // the image renders inside that container is what actually makes it
    // clickable for the lightbox. The click itself is a real-browser check
    // (see the PR description) since Pest has no JS runner.
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();

    $img = (new DOMXPath($document))->query('//*[@data-description-field]//*[@data-rich-text-content]//img')->item(0);
    expect($img)->not->toBeNull();
    expect($img->getAttribute('src'))->toContain('a.jpg');
});

test('the Save/Cancel buttons from before the autosave-on-blur change are gone entirely, and the read-only view is bordered like every other field', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk();

    $response->assertDontSee('save-description-btn', false)
        ->assertDontSee('cancel-description-btn', false);

    // .description-view-row itself carries the standard field border/
    // radius/background (rounded-md border border-gray-300 bg-white),
    // the same box Title's own <input> uses — not bare unboxed text.
    $page = $response->getContent();
    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();

    $viewRow = (new DOMXPath($document))->query('//*[contains(@class, "description-view-row")]')->item(0);
    expect($viewRow)->not->toBeNull();
    expect($viewRow->getAttribute('class'))->toContain('border');
});

test('the Edit control only appears for a user with edit permission on this task', function () {
    $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()
        ->assertSee('edit-description-btn', false);

    $client = makeClientWithProjectAccessForDescription($this->org, $this->project);

    $noEditPage = $this->actingAs($client)->get("/tasks/{$this->task->id}/edit")->assertOk();
    $noEditPage->assertDontSee('edit-description-btn', false);
    // Still readable, and the image is still there for the lightbox — just
    // no path into edit mode at all (the whole $canEdit-gated form,
    // including this field, is replaced by the read-only summary block —
    // see tasks/edit.blade.php's @else branch).
    $noEditPage->assertSee('data-rich-text-content', false);
    $noEditPage->assertSee('a.jpg', false);
});

/**
 * task #4 follow-up: sticky Edit button. The actual "still visible after
 * scrolling past its original spot" behavior is a real-browser check (see
 * the PR description) — Pest can't scroll a rendered page — but the
 * server-rendered `sticky` class is the one thing an HTTP response can
 * verify, and it's gated by the exact same $canEdit permission check the
 * button's own existence already was (see the test above): there's no
 * separate "make it sticky" flag to get out of sync with "should this user
 * see the button at all" in the first place, since it's the same element.
 */
test('the Edit button is sticky, and only for a user who has edit permission at all', function () {
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();

    $editBtn = (new DOMXPath($document))->query('//*[contains(@class, "edit-description-btn")]')->item(0);
    expect($editBtn)->not->toBeNull();
    expect($editBtn->getAttribute('class'))->toContain('sticky');

    // A user with no edit permission never gets this element at all — so
    // there's no sticky button for them to see, at any scroll position.
    $client = makeClientWithProjectAccessForDescription($this->org, $this->project);
    $this->actingAs($client)->get("/tasks/{$this->task->id}/edit")->assertOk()
        ->assertDontSee('edit-description-btn', false);
});

test('a user without edit permission cannot update the description via a direct request either, not just a hidden button', function () {
    $client = makeClientWithProjectAccessForDescription($this->org, $this->project);

    $this->actingAs($client)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => $this->task->title,
        'description' => '<p>Hacked</p>',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertForbidden();

    expect($this->task->fresh()->description)->not->toContain('Hacked');
});

test('the fallback hidden input still submits the untouched description correctly when only some other field changes', function () {
    // The scenario this field exists for: Description is never put into
    // edit mode, but the user changes something else (Priority here) and
    // clicks the page's main Save changes button.
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => $this->task->title,
        'description' => descriptionFallbackInputValue(
            $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->getContent()
        ),
        'priority' => 'high',
        'status' => 'pending',
    ])->assertRedirect();

    $fresh = $this->task->fresh();
    expect($fresh->priority->value)->toBe('high');
    expect($fresh->description)->toContain('<p>Hello</p>')->toContain('a.jpg');
});

test('the Add Task page is unaffected — Description is still a live editor there immediately, no view/edit split', function () {
    // Scope check (task #4): this fix is specifically for the Edit Task
    // page. The Add Task page has no existing content to "view" yet, so it
    // keeps showing the live TipTap editor immediately, exactly as before.
    $page = $this->actingAs($this->management)->get('/tasks/create')->assertOk()->getContent();

    expect(richTextEditorNode($page, 'Description'))->not->toBeNull();
});
