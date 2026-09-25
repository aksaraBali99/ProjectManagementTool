<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;

/**
 * Covers a fix on the Edit Task page: the inline "+ Add new document"
 * mini-form (tasks/_documents.blade.php) used to be gated by $canEdit
 * (TaskPolicy::update) — the wrong capability. Creating a document is
 * DocumentPolicy::create()/manage_documents, a genuinely different
 * capability that only happened to overlap with task-editing for most
 * roles so far. The clearest case where they diverge: a Staff assignee
 * can edit their own task via TaskPolicy::update()'s unconditional
 * assignee bypass, with no create_edit_tasks permission at all, and
 * Staff doesn't hold manage_documents by default either — so this user
 * passes $canEdit but must NOT see the create-document button.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->department = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    // Staff, no department access, no create_edit_tasks, no
    // manage_documents — the assignee bypass is the ONLY reason this user
    // can edit the task at all.
    $this->staffAssignee = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->staffAssignee->id,
        'role_id' => Role::where('slug', 'staff')->firstOrFail()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->department->id,
        'assignee_id' => $this->staffAssignee->id,
        'title' => 'Ship it',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

test('a staff assignee can edit the task via the assignee bypass but does not see the Add Document button', function () {
    $response = $this->actingAs($this->staffAssignee)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();

    // canEdit is genuinely true for this user — other editable fields
    // render normally, this isn't a page that quietly locked everything.
    $response->assertSee('value="Ship it"', false);

    $response->assertDontSee('+ Add new document');
});

test('the same staff assignee still sees the attach-existing-document controls, since that stays under canEdit', function () {
    $response = $this->actingAs($this->staffAssignee)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk();
    // The attach-existing-document <select>'s placeholder option — unlike
    // the .attach-document-btn CSS class, which the partial's own <script>
    // block also references unconditionally, this text only renders
    // inside the @if ($canEdit) attach section itself.
    $response->assertSee('Select a document…');
});

test('granting manage_documents to Staff makes the Add Document button appear for the same assignee', function () {
    $staffRole = Role::where('slug', 'staff')->firstOrFail();
    $manageDocumentsId = \App\Models\Permission::where('slug', 'manage_documents')->firstOrFail()->id;
    $staffRole->permissions()->syncWithoutDetaching([$manageDocumentsId]);

    $response = $this->actingAs($this->staffAssignee)->get("/tasks/{$this->task->id}/edit");

    $response->assertOk()->assertSee('+ Add new document');
});
