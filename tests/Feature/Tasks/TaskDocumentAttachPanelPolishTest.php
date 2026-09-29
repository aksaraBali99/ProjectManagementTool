<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * Covers the 3-part "attach panel polish" pass on the merged attach panel
 * (see TaskDocumentMergedPanelTest for the panel's own markup/ARIA/gate
 * coverage, which this file doesn't repeat):
 *   1. The results-list heading's default ("Recently added") state.
 *   2. The custom-styled file input's accessible markup.
 *   3. The Upload tab's CSS-based field reordering vs. the unchanged
 *      Add link tab.
 *
 * The dynamic parts of 1 and 2 (heading toggling as the user types, the
 * filename display updating with no visual overlap, keyboard-driving the
 * file picker) are JS-only behavior Pest can't execute against a page —
 * those were verified live in a browser instead (see the PR description).
 */
beforeEach(function () {
    $this->owner = createOwner();
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);

    $this->task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'T', 'priority' => 'medium', 'status' => 'pending',
    ]);
});

test('the attach panel renders with "Recently added" as its default (empty-query) results heading', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    expect($content)->toContain('attach-document-results-heading');
    // The static, server-rendered heading and its aria-label are the
    // "Recently added" default — "Matching documents" still appears
    // elsewhere in the page (inside the JS that toggles the heading as
    // the user types), so this asserts the specific rendered elements
    // rather than the whole page's text.
    expect($content)->toContain('<p class="attach-document-results-heading text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Recently added</p>');
    expect($content)->toContain('aria-label="Recently added"');
});

test('the upload panel exposes a custom file control: an sr-only native input paired with a label, plus a separate filename display', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    // The native input stays real and keyboard-operable — sr-only, never
    // display:none/visibility:hidden — and is bound to a visible label
    // (not a second, independently-focusable button), so Tab reaches
    // exactly one control here.
    expect($content)->toContain('id="new-document-file-'.$this->task->id.'"');
    expect($content)->toContain('class="new-document-file sr-only"');
    expect($content)->toContain('for="new-document-file-'.$this->task->id.'"');
    expect($content)->toContain('Choose file');

    // A separate element carries the selected filename (or its default
    // text) so the trigger button's own label never has to double as
    // the filename display — this is what removes the old browser-
    // default "Choose File"-over-filename overlap.
    expect($content)->toContain('new-document-file-name');
    expect($content)->toContain('No file selected');
});

test('the upload panel is CSS-ordered ahead of the name field while the link panel keeps the default order', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    // Both mode panels sit in the same DOM position (after the name
    // field) in source order; only the upload one is pulled ahead
    // visually via CSS `order`, since it's the only one of the two whose
    // tab wants a non-default field order. This is what the panel's own
    // comment documents: the link tab's order is left untouched because
    // the upload panel's `order-first` never applies while it's hidden.
    expect($content)->toContain('<div class="new-document-panel" data-panel="link">');
    expect($content)->toContain('<div class="new-document-panel hidden order-first" data-panel="upload">');
});

test('the create form JS auto-fills the name from the selected filename (extension stripped), but only when the name field is still empty', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    // Structural guard for the auto-fill behavior verified live in a
    // browser: the extension-stripping helper exists, and the change
    // handler only assigns into the name field when it's currently
    // empty — so clicking "Upload [name] as a new file" on a search
    // result (which pre-fills the name) is never clobbered by a
    // subsequent file selection.
    expect($content)->toContain('function stripExtension(filename)');
    expect($content)->toContain('nameInput && ! nameInput.value && file');
});
