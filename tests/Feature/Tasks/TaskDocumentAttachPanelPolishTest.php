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

test('the create form JS wires the shared name-autofill module and resets it on Back/close/open so no attempt leaks into the next one', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    // Structural guard for the auto-fill behavior verified live in a
    // browser: the actual extension-stripping + "never clobber a manual
    // edit" tracking now lives in resources/js/document-name-autofill.js
    // (shared with the Documents page's own upload form — see its own
    // unit-style coverage), so this page just needs to wire it up and
    // protect a search-derived pre-fill the same way a manual edit is.
    expect($content)->toContain('window.solavaDocumentNameAutofill.wireFileNameAutofill(');
    expect($content)->toContain('nameAutofill.protect();');

    // task #73 (code-review follow-up): resetNewDocumentForm() clears the
    // file/name/tracking on Back, on closing the panel, and on reopening
    // it — previously nothing reset either, so selecting a file, clicking
    // Back, then starting a DIFFERENT attempt left the old file AND its
    // derived name silently in place.
    expect($content)->toContain('function resetNewDocumentForm()');
    expect(substr_count($content, 'resetNewDocumentForm();'))->toBeGreaterThanOrEqual(4);
});

test('the existing-document picker\'s icon helper mirrors Document::iconClass() (image/audio/video by mime prefix), not just "has a mime_type or not"', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    // task #73 (code-review follow-up): the picker used to only tell
    // "has a mime_type" (generic file icon) apart from "doesn't" (link
    // icon), so an attached image/audio/video result rendered with the
    // same generic file icon the standalone Documents page had already
    // moved past for the same document.
    expect($content)->toContain("doc.mime_type.indexOf('image/') === 0) return 'ti-photo'");
    expect($content)->toContain("doc.mime_type.indexOf('audio/') === 0) return 'ti-music'");
    expect($content)->toContain("doc.mime_type.indexOf('video/') === 0) return 'ti-video'");
});

test('the create form JS disables the submit button while a create-and-attach request is in flight, guarding against a double-submit', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    // Structural guard for the double-submit fix verified live in a
    // browser: without this, a double-click or a slow network plus an
    // impatient second click fires two POST /documents requests before
    // the first resolves, each succeeding independently (two distinct
    // new Document rows, both attached to the task, since there's no
    // document_id collision for the linker to reject) - a silent
    // duplicate with no error shown. Mirrors the picker's own
    // attachDocument(), which already disables its trigger the same way.
    expect($content)->toContain('createBtn.disabled = true;');
    expect($content)->toContain('createBtn.disabled = false;');
});

test('the results list JS handles arrow-key/Home/End navigation between result buttons', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    // Structural guard for the keyboard-nav fix verified live in a
    // browser: the picker's results used to be a native <select> (free
    // arrow-key navigation, type-ahead, Home/End); replacing it with
    // individual <button> rows regressed a keyboard-only user to Tabbing
    // through every result one at a time. This restores Up/Down/Home/End
    // as an addition on top of unchanged native Tab behavior - no roving
    // tabindex, no listbox/option ARIA reinterpretation of what are still
    // plain buttons.
    expect($content)->toContain("resultsEl.addEventListener('keydown'");
    expect($content)->toContain("['ArrowDown', 'ArrowUp', 'Home', 'End']");
});
