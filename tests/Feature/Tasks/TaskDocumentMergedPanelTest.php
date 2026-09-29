<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

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

    // Panel: labelled dialog, search input has an accessible name, a
    // "Matching documents" heading, and a labelled results list.
    expect($content)->toContain('role="dialog"');
    expect($content)->toContain('Search or attach a document');
    expect($content)->toContain('Matching documents');
    expect($content)->toContain('aria-label="Matching documents"');

    // The two create-instead action rows, always present (default/empty
    // state — no search performed yet), not conditional on any query.
    $response->assertSee('Upload a new file');
    $response->assertSee('Add a link');
});

test('a Client-role user (who could previously create-and-attach via "+ Add new document") sees no document button at all now', function () {
    // task #73 (UI merge): a deliberate, flagged narrowing — the merged
    // panel's single gate is TaskPolicy::attachDocuments(), which excludes
    // Client unconditionally, unlike the old "+ Add new document"
    // button's own gate (DocumentPolicy::create(), which Client could
    // pass with manage_documents). This regression-guards that the
    // narrowing is what actually ships, not an accidental leftover path.
    $clientRole = Role::where('slug', 'client')->firstOrFail();
    $manageDocumentsId = Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $clientRole->permissions()->syncWithoutDetaching([$manageDocumentsId]);

    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => $clientRole->id]);
    $this->project->clients()->attach($client->id);

    $response = $this->actingAs($client)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    $response->assertDontSee('Attach document');
    $response->assertDontSee('+ Add new document');
    $response->assertDontSee('Attach existing');
});
