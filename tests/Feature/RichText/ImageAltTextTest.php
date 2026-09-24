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
 * Alt text for embedded images (task #4, alt text) — image-specific,
 * never video/audio/document (those have no accessibility-relevant "alt"
 * concept the same way). Stored as the image's own real `alt` HTML
 * attribute (App\Support\RichText's sanitizer already allowlists it
 * alongside src/width/height — see ->allowElement('img', [...])), not a
 * separate database column, so persistence/round-tripping is exactly the
 * same generic img-attribute path width/height (resize) already uses.
 *
 * What Pest CANNOT verify: the actual DEFAULT-fallback computation
 * itself (the original filename for a picker/drop upload, or the
 * server-resolved name for a clipboard paste) happens entirely
 * client-side, in buildImageUpload()'s own upload() — a picker/paste/
 * drop response never even echoes a "chosen alt" back from the server,
 * there's nothing here to assert on. Nor can it drive the low-friction
 * popover prompt itself, or the "Edit alt text" button that reopens it
 * for an already-embedded image. All three were confirmed manually: a
 * dropped image's popover pre-filled with its real filename, a pasted
 * image's with the auto-generated name, editing an existing image's alt
 * text via the button, and that doing so does NOT prematurely autosave
 * the whole form (a real bug caught in that pass — see the isBlurSettled
 * usage in rich-text-editor.js).
 *
 * What Pest DOES verify, and what these tests cover: once some alt value
 * exists on a saved <img>, the server-side round trip through
 * RichText::toHtml()/normalize() never drops or empties it — on its own,
 * alongside a resize, and across a later edit that changes it again.
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
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function altTextTaskUpdate(Task $task, string $description): array
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

test('an image saved with a real alt attribute is not stripped or emptied on save — not left with no alt at all', function () {
    $html = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" alt="A screenshot of the login screen">';

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", altTextTaskUpdate($this->task, $html))->assertRedirect();

    expect($this->task->fresh()->description)->toContain('alt="A screenshot of the login screen"');
});

test('alt text set at upload time is retrievable afterward, and editable to something else', function () {
    // The name a picker/drop upload would already know, or a pasted
    // upload's auto-generated one — set immediately at insert time
    // client-side, so this is what "afterward" reads: an ordinary
    // already-saved img, exactly like reloading the Edit Task page would
    // show.
    $original = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" alt="draft-diagram">';
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", altTextTaskUpdate($this->task, $original))->assertRedirect();
    expect($this->task->fresh()->description)->toContain('alt="draft-diagram"');

    // Editing it later (the "Edit alt text" button reopening the same
    // popover, pre-filled with the current value) is just another save
    // of the same field with a different value — no separate endpoint.
    $edited = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" alt="Architecture diagram for the payments service">';
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", altTextTaskUpdate($this->task, $edited))->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)->toContain('alt="Architecture diagram for the payments service"')
        ->not->toContain('draft-diagram');
});

test('alt text survives a resize-and-save round trip on the same image, and vice versa', function () {
    // Both attributes set together, exactly as a real resize commit
    // (updateAttributes({width, height}) — never touches alt) would
    // leave the node: the width/height MediaDimensionAttributeSanitizer
    // enforces and the alt the sanitizer separately allowlists are
    // independent attributes on the same allowlisted <img> element,
    // never conditional on each other.
    $html = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" alt="Team photo" width="400" height="300">';

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", altTextTaskUpdate($this->task, $html))->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)
        ->toContain('alt="Team photo"')
        ->toContain('width="400"')
        ->toContain('height="300"');

    // Reload: the same three attributes render back out together, the
    // same read-only view a real resize's own follow-up save would show.
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $editorContent = descriptionViewContent($page);
    expect($editorContent)
        ->toContain('alt="Team photo"')
        ->toContain('width="400"')
        ->toContain('height="300"');

    // And a SUBSEQUENT resize (a new width/height only) leaves the
    // existing alt untouched — the scenario this task's own instruction
    // specifically flagged: one attribute-persistence fix must not
    // regress another attribute on the same element.
    $resized = '<img src="https://cdn.example.com/tasks/1/images/a.jpg" alt="Team photo" width="600" height="450">';
    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", altTextTaskUpdate($this->task, $resized))->assertRedirect();

    $afterResize = $this->task->fresh()->description;
    expect($afterResize)
        ->toContain('alt="Team photo"')
        ->toContain('width="600"')
        ->toContain('height="450"')
        ->not->toContain('width="400"');
});

test('alt text is not video/audio/document specific — the sanitizer never allows an alt attribute on those elements', function () {
    // Alt text is an image accessibility concept specifically (task #4,
    // alt text's own scope) — confirms the sanitizer allowlist agrees:
    // an alt attribute on any of the other three embeddable elements is
    // stripped, not silently accepted.
    $html = '<audio src="https://cdn.example.com/tasks/1/audio/a.mp3" controls alt="should not survive"></audio>'
        .'<video src="https://cdn.example.com/tasks/1/video/a.mp4" controls alt="should not survive"></video>'
        .'<p><file-chip href="https://cdn.example.com/tasks/1/documents/a.pdf" alt="should not survive">report.pdf</file-chip></p>';

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", altTextTaskUpdate($this->task, $html))->assertRedirect();

    expect($this->task->fresh()->description)->not->toContain('alt="should not survive"');
});
