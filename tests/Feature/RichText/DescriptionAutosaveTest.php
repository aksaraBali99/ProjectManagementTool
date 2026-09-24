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
 * Task Description's editor is permanently live and autosaves on blur
 * (task #4, description autosave) — a deliberate revert of an earlier
 * view/edit split (see git history / DescriptionViewEditModeTest.php,
 * now removed) back to the Add Task page's own always-live shape: no
 * read-only view, no Edit control, no Save/Cancel buttons, bordered like
 * every other field on this form via .rte's own default border.
 *
 * The actual autosave interaction (typing, clicking out, the form
 * submitting on its own) can't be driven from Pest — there's no JS
 * runner in this suite, the same caveat every other client-behavior test
 * here already documents — confirmed instead via manual browser testing.
 * These tests pin the structural, server-checkable half: the editor is
 * there immediately with no button UI left over from the reverted split,
 * and saving Description still works exactly like any other field on
 * this same form.
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

function makeClientWithProjectAccessForDescriptionAutosave(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);
    $project->clients()->attach($client->id);

    return $client;
}

test('Description renders as a live editor immediately on the Edit Task page — no read-only view, no Edit button to click first', function () {
    $response = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk();
    $response->assertDontSee('edit-description-btn', false);

    $page = $response->getContent();
    expect(richTextEditorNode($page, 'Description'))->not->toBeNull();
    expect(descriptionViewContent($page))->toContain('<p>Hello</p>')->toContain('<img');
});

test('the Save/Cancel button markup from the reverted view/edit split is gone entirely', function () {
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk();

    $page->assertDontSee('save-description-btn', false)
        ->assertDontSee('cancel-description-btn', false)
        ->assertDontSee('description-action-btn', false);
});

test('the Description editor is bordered like every other field on the form, not left unstyled', function () {
    // .rte itself carries the standard border/radius/background (see
    // app.css) unconditionally, not just on focus — this just confirms
    // the editor's root class actually reaches the page (the field is no
    // longer ever rendered as bare read-only text with no box at all).
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    $node = richTextEditorNode($page, 'Description');
    expect($node->getAttribute('class'))->toContain('rte');
});

test('Description still saves correctly through the same Edit Task form submit as before — no separate persistence path introduced', function () {
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => $this->task->title,
        'description' => '<p>Updated via the autosaving editor</p>',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    expect($this->task->fresh()->description)->toBe('<p>Updated via the autosaving editor</p>');
});

test('a user without edit permission on this task still gets the read-only summary, never the live editor', function () {
    $client = makeClientWithProjectAccessForDescriptionAutosave($this->org, $this->project);

    $page = $this->actingAs($client)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    expect(richTextEditorNode($page, 'Description'))->toBeNull();
    // Still readable via the ordinary read-only render the @else branch
    // uses — permission scoping is unaffected by the autosave change.
    expect($page)->toContain('data-rich-text-content')->toContain('a.jpg');
});

test('a user without edit permission cannot update the description via a direct request either', function () {
    $client = makeClientWithProjectAccessForDescriptionAutosave($this->org, $this->project);

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
