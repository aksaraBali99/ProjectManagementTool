<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * Task #4 phase 6 — a verification pass, not new feature work: confirms the
 * pre-existing Documents feature's "always opens in a new tab, never an
 * inline preview/embed/lightbox" behavior still holds after everything
 * built in Phases 2-5 (TipTap editor swap, image lightbox, audio/video
 * inline embedding, video resize + its own lightbox) and the view/edit
 * mode split. Documents predate all of that work and were never wired
 * into the rich-text pipeline, but this phase exists specifically because
 * "inline embedding" and "click-to-expand" were built for three OTHER
 * media types across several closely-timed phases — a plausible way for
 * that behavior to have blurred onto documents too, whether by a shared
 * CSS class catching more than intended or a copy-pasted delegation
 * listener. Verified clean; see the PR description for the full account
 * of what was checked and why nothing needed to change.
 *
 * A fourth surface named in the task — a "file-chip" node embedding a
 * Document from inside the Description/Comments editor — does not exist
 * in this codebase (no RichTextDocumentController, no chip-related JS);
 * skipped per the task's own instruction to skip a feature that hasn't
 * been built yet. Whoever adds that feature later should extend this file
 * with the same two assertions (new-tab attributes; not inline/embedded)
 * rather than starting a separate test file for it.
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
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->document = Document::create([
        'organization_id' => $this->org->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Project brief.pdf',
        'link' => 'https://example.com/project-brief.pdf',
        'access_level' => 'internal',
    ]);
});

/**
 * Every markup shape that Phases 2-5's inline-embedding work introduced —
 * checked together so this one assertion is the single source of truth
 * for "does not look embedded", reused by both surfaces below.
 */
function assertNotRenderedAsInlineMedia(string $html, string $documentName): void
{
    // Isolate the region immediately around the document's own name/link,
    // not the whole page — the page legitimately contains real <img>/
    // <video> elsewhere (nav icons, an unrelated task's embedded image),
    // so a whole-page assertion would pass for the wrong reason.
    $position = strpos($html, $documentName);
    expect($position)->not->toBeFalse();

    $window = substr($html, max(0, $position - 400), 800);

    expect($window)
        ->not->toContain('<img')
        ->not->toContain('<video')
        ->not->toContain('<audio')
        ->not->toContain('video-thumb')
        ->not->toContain('lightbox')
        ->not->toContain('data-rich-text-content');
}

test('the standalone Documents page lists a document as a plain new-tab link', function () {
    $response = $this->actingAs($this->management)->get("/documents/{$this->org->id}");

    $response->assertOk();
    $html = $response->getContent();

    expect($html)
        ->toContain('href="https://example.com/project-brief.pdf"')
        ->toContain('target="_blank"')
        ->toContain('rel="noopener noreferrer"');

    assertNotRenderedAsInlineMedia($html, 'Project brief.pdf');
});

test('a document attached to a task via task_documents opens as a plain new-tab link on the Edit Task page', function () {
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $task->documents()->attach($this->document->id);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $html = $response->getContent();

    expect($html)
        ->toContain('href="https://example.com/project-brief.pdf"')
        ->toContain('target="_blank"')
        ->toContain('rel="noopener"');

    assertNotRenderedAsInlineMedia($html, 'Project brief.pdf');
});

test('the task list drilldown does not render attached documents at all — only Description, Subtasks and Comments live there', function () {
    // Documents-per-task are shown exclusively on the Edit Task page
    // (tasks/_documents.blade.php) — confirming the *other* task surface
    // (the list drilldown tasks/index.blade.php renders) has no separate,
    // possibly-differently-behaved rendering path for the same data.
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $task->documents()->attach($this->document->id);

    $response = $this->actingAs($this->management)->get("/tasks/{$this->org->id}");

    $response->assertOk();
    $response->assertDontSee('Project brief.pdf');
});

test('a document\'s own link text typed into a task description stays a plain link, not an embed', function () {
    // The one place a document's URL could plausibly end up inside
    // rich-text content: someone pastes/types it directly into the
    // Description. RichText's sanitizer already forces every <a> to open
    // safely — this confirms that generic link handling, not anything
    // document-specific, is what's in play, and that it still renders as
    // an <a>, never upgraded to a media embed by any Phase 2-5 code.
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'description' => '<p>See <a href="https://example.com/project-brief.pdf">the brief</a></p>',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $response = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit");

    $response->assertOk();
    $html = $response->getContent();

    expect($html)
        ->toContain('href="https://example.com/project-brief.pdf"')
        ->toContain('target="_blank"')
        ->toContain('rel="noopener noreferrer nofollow"');

    $position = strpos($html, 'the brief');
    expect($position)->not->toBeFalse();
    $window = substr($html, max(0, $position - 200), 400);
    expect($window)->not->toContain('<img')->not->toContain('<video')->not->toContain('video-thumb');
});
