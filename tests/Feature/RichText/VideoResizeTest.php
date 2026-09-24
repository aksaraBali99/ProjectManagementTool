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
 * Drag-to-resize a video (task #4, video resize + lightbox follow-up)
 * doesn't add a new save path — a resized video is just a <video> with
 * width/height HTML attributes, saved through the exact same Description/
 * Comment endpoints every other rich-text edit already goes through — same
 * shape as ImageResizeTest.php, and same reasoning: the actually-risky
 * part is whether the server keeps width/height across a full save/reload
 * round trip (the documented "resize silently vanishes on reload" failure
 * pattern), which here specifically depends on RichText's sanitizer
 * allowlist carrying width/height for <video> too (it does — see
 * RichText::sanitizer()'s ->allowElement('video', [...]) and
 * MediaDimensionAttributeSanitizer, confirmed generic across img/video
 * rather than assumed), not on a duplicate Video registration (there's
 * exactly one, resizable-video.js).
 *
 * What the resize handles and the click-to-expand thumbnail/play-icon/
 * lightbox actually look like and do is pure client-side JS
 * (resizable-video.js, video-thumbnail.js, lightbox.js) that Pest can't
 * execute — verified separately via manual browser testing, the same way
 * this whole multi-phase feature's actual drag/click interactions always
 * have been.
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
        'description' => '<video src="https://cdn.example.com/tasks/1/video/a.webm" controls></video>',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function videoResizeTaskPayload(Task $task, string $description): array
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

test('a video resized in the editor and then saved persists its new width and height', function () {
    $resized = '<video src="https://cdn.example.com/tasks/1/video/a.webm" controls width="400" height="225"></video>';

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", videoResizeTaskPayload($this->task, $resized))
        ->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)
        ->toContain('width="400"')
        ->toContain('height="225"')
        ->toContain('src="https://cdn.example.com/tasks/1/video/a.webm"');
});

test('reloading the Edit Task page after a resize-and-save shows the resized dimensions, not the default size', function () {
    // The specific bug pattern flagged for this feature: a resize that
    // "sticks" through one response but is lost the moment the value is
    // read back out of storage and re-rendered — sanitized on save
    // (normalize) AND again on display (toHtml), so both passes have to
    // agree on keeping width/height.
    $resized = '<video src="https://cdn.example.com/tasks/1/video/a.webm" controls width="250" height="140"></video>';

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", videoResizeTaskPayload($this->task, $resized))
        ->assertRedirect();

    $editPage = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $editorContent = descriptionViewContent($editPage);

    expect($editorContent)->toContain('width="250"')->toContain('height="140"');

    // Not just the editor's own initial-content payload — the read-only
    // task list drilldown renders the same stored value independently.
    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee('width="250"', false)
        ->assertSee('height="140"', false);
});

test('a video embedded before this change (no width/height at all) still displays correctly at its original default sizing', function () {
    // $this->task's own seeded description, from beforeEach, is exactly
    // this: a video with no resize data, as every video saved under phase
    // 5 (before this feature existed) looks.
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $editorContent = descriptionViewContent($page);

    expect($editorContent)
        ->toContain('src="https://cdn.example.com/tasks/1/video/a.webm"')
        ->not->toContain('width=')
        ->not->toContain('height=');

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee('src="https://cdn.example.com/tasks/1/video/a.webm"', false);

    // Untouched by merely being viewed.
    expect($this->task->fresh()->description)->toBe('<video src="https://cdn.example.com/tasks/1/video/a.webm" controls></video>');
});

test('a resized video works the same way inside a comment', function () {
    $resized = '<p>See attached</p><video src="https://cdn.example.com/tasks/1/video/b.webm" controls width="320" height="180"></video>';

    $created = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $resized]);
    $created->assertCreated();
    $created->assertJsonPath('comment.body_html', fn ($html) => str_contains($html, 'width="320"') && str_contains($html, 'height="180"'));

    $comment = Comment::firstOrFail();
    expect($comment->body)->toContain('width="320"')->toContain('height="180"');

    // Reload via the polling endpoint (what the drilldown re-renders from).
    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")
        ->assertOk()
        ->assertJsonPath('comments.0.body_html', fn ($html) => str_contains($html, 'width="320"') && str_contains($html, 'height="180"'));

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee('width="320"', false)
        ->assertSee('height="180"', false);
});

test('editing a comment to resize its video keeps the new dimensions on reload', function () {
    $comment = Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $this->management->id,
        'body' => '<video src="https://cdn.example.com/tasks/1/video/c.webm" controls></video>',
    ]);

    $this->actingAs($this->management)->putJson("/comments/{$comment->id}", [
        'body' => '<video src="https://cdn.example.com/tasks/1/video/c.webm" controls width="500" height="281"></video>',
    ])->assertOk()->assertJsonPath('comment.body_html', fn ($html) => str_contains($html, 'width="500"'));

    expect($comment->fresh()->body)->toContain('width="500"')->toContain('height="281"');
});

test('an implausible width/height from a direct request is dropped, not trusted from the client', function () {
    $tampered = '<video src="https://cdn.example.com/tasks/1/video/a.webm" controls width="999999" height="abc"></video>';

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", videoResizeTaskPayload($this->task, $tampered))
        ->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)
        ->not->toContain('999999')
        ->not->toContain('abc')
        ->toContain('src="https://cdn.example.com/tasks/1/video/a.webm"');
});

test('resizing does not affect an unrelated field — updating the title alone leaves an existing resize intact', function () {
    $this->task->update(['description' => '<video src="https://cdn.example.com/tasks/1/video/a.webm" controls width="400" height="225"></video>']);

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", [
        'project_id' => $this->task->project_id,
        'department_id' => $this->task->department_id,
        'title' => 'Renamed',
        'description' => $this->task->description,
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    expect($this->task->fresh()->description)->toContain('width="400"')->toContain('height="225"');
});

test('resizing an image and a video independently in the same description keeps both sets of dimensions distinct', function () {
    // MediaDimensionAttributeSanitizer is shared across both elements
    // (confirmed generic, not assumed) — this is the one test that would
    // catch it accidentally cross-contaminating them, e.g. reading the
    // wrong element's width when both appear in the same document.
    $both = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" width="150" height="100">'
        .'<video src="https://cdn.example.com/tasks/1/video/a.webm" controls width="400" height="225"></video>';

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", videoResizeTaskPayload($this->task, $both))
        ->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)
        ->toContain('src="https://cdn.example.com/tasks/1/images/a.jpg"')
        ->toContain('width="150"')
        ->toContain('height="100"')
        ->toContain('src="https://cdn.example.com/tasks/1/video/a.webm"')
        ->toContain('width="400"')
        ->toContain('height="225"');
});
