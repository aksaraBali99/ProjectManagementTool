<?php

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

/**
 * Drag-to-resize (task #4) doesn't add a new save path — a resized image is
 * just an <img> with width/height HTML attributes, saved through the exact
 * same Description/Comment endpoints every other rich-text edit already
 * goes through. These tests are really about the one thing that's actually
 * new and actually risky here: whether the server keeps those two
 * attributes across a full save/reload round trip, rather than the
 * documented "resize silently vanishes on reload" failure mode — which
 * would happen here specifically if RichText's sanitizer allowlist didn't
 * carry width/height (it does, see RichText::sanitizer()), not from a
 * duplicate Image registration (there's exactly one, resizable-image.js).
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
        'description' => '<img src="https://cdn.example.com/tasks/1/images/a.jpg">',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function imageResizeTaskPayload(Task $task, string $description): array
{
    return [
        'project_id' => $task->project_id,
        'department_id' => $task->department_id,
        'title' => $task->title,
        'description' => $description,
        'priority' => 'medium',
        'status' => 'pending',
    ];
}

test('an image resized in the editor and then saved persists its new width and height', function () {
    $resized = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" width="400" height="300">';

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", imageResizeTaskPayload($this->task, $resized))
        ->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)
        ->toContain('width="400"')
        ->toContain('height="300"')
        ->toContain('src="https://cdn.example.com/tasks/1/images/a.jpg"');
});

test('reloading the Edit Task page after a resize-and-save shows the resized dimensions, not the default size', function () {
    // The specific bug pattern flagged for this feature: a resize that
    // "sticks" through one response but is lost the moment the value is
    // read back out of storage and re-rendered — sanitized on save
    // (normalize) AND again on display (toHtml), so both passes have to
    // agree on keeping width/height.
    $resized = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" width="250" height="180">';

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", imageResizeTaskPayload($this->task, $resized))
        ->assertRedirect();

    $editPage = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $editorContent = richTextEditorContent($editPage, 'Description');

    expect($editorContent)->toContain('width="250"')->toContain('height="180"');

    // Not just the editor's own initial-content payload — the read-only
    // task list drilldown renders the same stored value independently.
    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee('width="250"', false)
        ->assertSee('height="180"', false);
});

test('an image embedded before this change (no width/height at all) still displays correctly at its original default sizing', function () {
    // $this->task's own seeded description, from beforeEach, is exactly
    // this: an img with no resize data, as every image saved under phase 3
    // (before this feature existed) looks.
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $editorContent = richTextEditorContent($page, 'Description');

    expect($editorContent)
        ->toContain('src="https://cdn.example.com/tasks/1/images/a.jpg"')
        ->not->toContain('width=')
        ->not->toContain('height=');

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee('src="https://cdn.example.com/tasks/1/images/a.jpg"', false);

    // Untouched by merely being viewed.
    expect($this->task->fresh()->description)->toBe('<img src="https://cdn.example.com/tasks/1/images/a.jpg">');
});

test('a resized image works the same way inside a comment', function () {
    $resized = '<p>See attached</p><img src="https://cdn.example.com/tasks/1/images/b.jpg" width="320" height="240">';

    $created = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $resized]);
    $created->assertCreated();
    $created->assertJsonPath('comment.body_html', fn ($html) => str_contains($html, 'width="320"') && str_contains($html, 'height="240"'));

    $comment = Comment::firstOrFail();
    expect($comment->body)->toContain('width="320"')->toContain('height="240"');

    // Reload via the polling endpoint (what the drilldown re-renders from).
    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")
        ->assertOk()
        ->assertJsonPath('comments.0.body_html', fn ($html) => str_contains($html, 'width="320"') && str_contains($html, 'height="240"'));

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee('width="320"', false)
        ->assertSee('height="240"', false);
});

test('editing a comment to resize its image keeps the new dimensions on reload', function () {
    $comment = Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $this->management->id,
        'body' => '<img src="https://cdn.example.com/tasks/1/images/c.jpg">',
    ]);

    $this->actingAs($this->management)->putJson("/comments/{$comment->id}", [
        'body' => '<img src="https://cdn.example.com/tasks/1/images/c.jpg" width="500" height="333">',
    ])->assertOk()->assertJsonPath('comment.body_html', fn ($html) => str_contains($html, 'width="500"'));

    expect($comment->fresh()->body)->toContain('width="500"')->toContain('height="333"');
});

test('an implausible width/height from a direct request is dropped, not trusted from the client', function () {
    $tampered = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" width="999999" height="abc">';

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", imageResizeTaskPayload($this->task, $tampered))
        ->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)
        ->not->toContain('999999')
        ->not->toContain('abc')
        ->toContain('src="https://cdn.example.com/tasks/1/images/a.jpg"');
});

test('resizing does not affect an unrelated field — updating the title alone leaves an existing resize intact', function () {
    $this->task->update(['description' => '<img src="https://cdn.example.com/tasks/1/images/a.jpg" width="400" height="300">']);

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => 'Renamed',
        'description' => $this->task->description,
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    expect($this->task->fresh()->description)->toContain('width="400"')->toContain('height="300"');
});
