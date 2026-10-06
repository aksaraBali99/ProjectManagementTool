<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Covers the UI merge folding the old separate "Attach existing" (Phase
 * 3's picker) and "+ Add new document" buttons/panels into ONE "Attach
 * document" entry point. The endpoints this panel drives — the picker
 * list, the attach action, and the upload/link creation forms — are all
 * unchanged (see TaskDocumentPickerTest and TaskDocumentAttachTest for
 * their own coverage); this file is only about the merged markup itself.
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

test('the old separate buttons never render for a user who can attach documents', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $response->assertDontSee('Attach existing');
    $response->assertDontSee('+ Add new document');
    $response->assertSee('Attach document');
});

test('the merged panel carries the required markup and ARIA attributes, and both create action rows are present by default', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $content = $response->getContent();

    // Toggle button: labelled dialog trigger.
    expect($content)->toContain('aria-haspopup="dialog"');
    expect($content)->toContain('aria-expanded="false"');
    expect($content)->toContain('aria-controls="attach-document-panel-'.$this->task->id.'"');

    // Panel: labelled dialog, search input has an accessible name, and a
    // labelled results list. The default (empty-query) heading is
    // "Recently added", not "Matching documents" — see
    // TaskDocumentAttachPanelPolishTest for the heading-toggle behavior.
    expect($content)->toContain('role="dialog"');
    expect($content)->toContain('Search or attach a document');
    expect($content)->toContain('Recently added');
    expect($content)->toContain('aria-label="Recently added"');

    // The two create-instead action rows, always present (default/empty
    // state — no search performed yet), not conditional on any query.
    $response->assertSee('Upload a new file');
    $response->assertSee('Add a link');
});

test('each attached document row shows who added it and when, matching the Documents page\'s own list', function () {
    $document = Document::create([
        'organization_id' => $this->org->id,
        'uploaded_by' => $this->management->id,
        'name' => 'Launch brief.pdf',
        'link' => 'https://example.com/launch-brief.pdf',
        'access_level' => 'internal',
    ]);
    $document->created_at = '2026-03-14 10:00:00';
    $document->save();
    $this->task->documents()->attach($document->id);

    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $response->assertSee($this->management->name);
    // Carbon's ->format('M j, Y') — matches the Documents page's own date
    // style for the same "uploaded by / when" information.
    $response->assertSee('Mar 14, 2026');
});

test('the attached-documents list resolves every row\'s uploader in one query, not one per document', function () {
    // Regression guard for the uploader eager-load added alongside the
    // author/date display: without ->with('uploader'), each row's
    // {{ $document->uploader->name }} would be a fresh "select ... from
    // users where id = ?" per document. This checks that specific query
    // shape directly rather than the page's total query count, since the
    // total already scales with document count for an unrelated,
    // pre-existing reason (the per-document Gate::allows('view', ...)
    // authorization filter above it isn't memoized either — out of scope
    // here, see DocumentPolicy::viewableIds()'s own docblock).
    $uploaders = collect(range(1, 5))->map(fn () => User::factory()->create());
    $uploaders->each(function (User $uploader, int $i) {
        OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $uploader->id, 'role_id' => Role::where('slug', 'management')->firstOrFail()->id]);
        $document = Document::create([
            'organization_id' => $this->org->id,
            'uploaded_by' => $uploader->id,
            'name' => "Doc {$i}.pdf",
            'link' => "https://example.com/doc-{$i}.pdf",
            'access_level' => 'internal',
        ]);
        $this->task->documents()->attach($document->id);
    });

    DB::enableQueryLog();
    $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk();
    // The eager load's own batch query has this exact shape (Eloquent's
    // relation-loading whereIn) — narrower than "any query mentioning
    // users.id", which also matches several unrelated permission/picker
    // queries the same page issues.
    $uploaderEagerLoadQueries = collect(DB::getQueryLog())->filter(fn ($query) => str_starts_with($query['query'], 'select * from "users" where "users"."id" in'));
    DB::flushQueryLog();

    expect($uploaderEagerLoadQueries)->toHaveCount(1);
});

/** A Client on this task's project, optionally holding manage_documents. */
function makeClientForMergedPanel(Organization $org, Project $project, bool $withManageDocuments): User
{
    $clientRole = Role::where('slug', 'client')->firstOrFail();

    if ($withManageDocuments) {
        $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
        $clientRole->permissions()->syncWithoutDetaching([$manageDocumentsId]);
    }

    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => $clientRole->id]);
    $project->clients()->attach($client->id);

    return $client;
}

/**
 * Counts REAL elements, not raw substrings. Most of these class names and
 * labels ("Upload a new file", "Recently added", ".attach-document-search")
 * also appear inside this view's own inline <script> — as querySelector
 * arguments or JS-built markup — so assertSee/assertDontSee on them matches
 * the script text and passes vacuously in BOTH directions. Script contents
 * are text nodes, never elements, so an XPath element query ignores them.
 *
 * @return array{toggle: string, searchInput: int, resultsList: int, folderToggle: int, folderPanel: int, uploadAction: int, linkAction: int}
 */
function mergedPanelElements(string $html): array
{
    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $hasClass = fn (string $class) => "contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')";

    $toggle = $xpath->query('//button['.$hasClass('attach-document-toggle').']');
    $panel = $xpath->query('//div['.$hasClass('attach-document-panel').']');
    $back = $xpath->query('//button['.$hasClass('new-document-back').']');

    return [
        'toggle' => $toggle->length ? trim($toggle->item(0)->textContent) : '',
        'panelLabel' => $panel->length ? $panel->item(0)->getAttribute('aria-label') : '',
        'backLabel' => $back->length ? trim($back->item(0)->textContent) : '',
        'searchInput' => $xpath->query('//input['.$hasClass('attach-document-search').']')->length,
        'resultsList' => $xpath->query('//ul['.$hasClass('attach-document-results').']')->length,
        'folderToggle' => $xpath->query('//button['.$hasClass('attach-folder-toggle').']')->length,
        'folderPanel' => $xpath->query('//div['.$hasClass('attach-folder-panel').']')->length,
        'uploadAction' => $xpath->query('//button['.$hasClass('attach-document-upload-action').']')->length,
        'linkAction' => $xpath->query('//button['.$hasClass('attach-document-link-action').']')->length,
    ];
}

test('a Client WITH manage_documents gets a create-only panel: Add document, upload and link, but no search or folders', function () {
    // The merged panel used to be gated solely on TaskPolicy::
    // attachDocuments(), which excludes Client unconditionally — so a
    // Client holding "Add & edit documents" had no way to add a document
    // here at all, even though DocumentController::store() would accept
    // it (forcing Public and attaching to the task). The panel now opens
    // on EITHER capability, with the browse-existing half omitted for
    // someone who only has create rights.
    $client = makeClientForMergedPanel($this->org, $this->project, withManageDocuments: true);

    $response = $this->actingAs($client)->get("/tasks/{$this->task->id}/edit");
    $response->assertOk();

    $el = mergedPanelElements($response->getContent());

    // What they CAN do: create a new document, by upload or by link.
    expect($el['toggle'])->toBe('Add document')
        ->and($el['uploadAction'])->toBe(1)
        ->and($el['linkAction'])->toBe(1);

    // Review follow-up: the dialog's accessible name and the Back button
    // both track the toggle, so a screen-reader user activating "Add
    // document" isn't announced into "Attach a document", and isn't
    // offered a way "back to search" they never had.
    expect($el['panelLabel'])->toBe('Add a document')
        ->and($el['backLabel'])->toBe('← Back');

    // What they must NOT get: any way to browse or attach the company's
    // existing documents, or to attach a folder — absent from the HTML
    // entirely, not merely hidden.
    expect($el['searchInput'])->toBe(0)
        ->and($el['resultsList'])->toBe(0)
        ->and($el['folderToggle'])->toBe(0)
        ->and($el['folderPanel'])->toBe(0);

    // "Search or attach a document/folder" appear only in markup, never in
    // this view's script, so these two stay safe as plain text assertions.
    $response->assertDontSee('Search or attach a document');
    $response->assertDontSee('Search or attach a folder');
});

test('a Client WITHOUT manage_documents still sees no document button at all', function () {
    $client = makeClientForMergedPanel($this->org, $this->project, withManageDocuments: false);

    $response = $this->actingAs($client)->get("/tasks/{$this->task->id}/edit");
    $response->assertOk();

    $el = mergedPanelElements($response->getContent());

    expect($el['toggle'])->toBe('')
        ->and($el['uploadAction'])->toBe(0)
        ->and($el['linkAction'])->toBe(0)
        ->and($el['searchInput'])->toBe(0)
        ->and($el['folderToggle'])->toBe(0);

    $response->assertDontSee('+ Add new document');
    $response->assertDontSee('Attach existing');
});

test('staff and management are unaffected: the full attach panel still renders with search and folders', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit");
    $response->assertOk();

    $el = mergedPanelElements($response->getContent());

    expect($el['toggle'])->toBe('Attach document')
        ->and($el['searchInput'])->toBe(1)
        ->and($el['resultsList'])->toBe(1)
        ->and($el['folderToggle'])->toBe(1)
        ->and($el['folderPanel'])->toBe(1)
        ->and($el['uploadAction'])->toBe(1)
        ->and($el['linkAction'])->toBe(1);

    // Unchanged wording for anyone who can actually attach.
    expect($el['panelLabel'])->toBe('Attach a document')
        ->and($el['backLabel'])->toBe('← Back to search');
});
