<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

/**
 * SOL-0022 subtask 3: a subtask description is rich text (links,
 * formatting, link chips), sanitized by the same RichText::normalize() as
 * the task Description and comments, capped at
 * Subtask::MAX_DESCRIPTION_LENGTH, and only ever shown through
 * RichText::toHtml(). The editor itself is JS (checked by hand, see the
 * PR); these tests cover what is stored, returned and rendered.
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
        'title' => 'Task',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $this->subtask = $this->task->subtasks()->create(['title' => 'Sub']);
});

/**
 * Pencil buttons in the server-rendered subtask rows (data-subtask-id).
 * Parsed, not searched for as text: the page's own script also holds the
 * markup for rows added later.
 */
function subtaskPencilCount(string $html): int
{
    $dom = new DOMDocument;
    @$dom->loadHTML($html);

    return (new DOMXPath($dom))
        ->query("//div[@data-subtask-id]//div[contains(@class, 'subtask-description-view-row')]//button[contains(@class, 'edit-subtask-description-btn')]")
        ->length;
}

function putSubtaskDescription($test, ?string $description)
{
    return $test->actingAs($test->management)->putJson("/subtasks/{$test->subtask->id}", ['description' => $description]);
}

test('formatting and links are kept, sanitized, and returned as safe HTML', function () {
    $response = putSubtaskDescription($this, '<p>Hi <strong>there</strong> <a href="https://example.com">link</a></p>')->assertOk();

    $stored = $this->subtask->fresh()->description;
    expect($stored)->toContain('<strong>there</strong>');
    expect($stored)->toContain('href="https://example.com"');

    $html = $response->json('description_html');
    expect($html)->toContain('<strong>there</strong>');
    expect($html)->toMatch('/<a [^>]*href="https:\/\/example\.com"/');
    expect($html)->toContain('target="_blank"');
    expect($html)->toContain('rel="noopener noreferrer nofollow"');
});

test('scripts, event handlers and javascript: links are stripped', function () {
    $response = putSubtaskDescription($this, '<p onclick="alert(1)">x <a href="javascript:alert(1)">bad</a></p><script>alert(1)</script><p>y</p>')->assertOk();

    foreach ([$this->subtask->fresh()->description, $response->json('description_html')] as $html) {
        expect($html)->not->toContain('<script');
        expect($html)->not->toContain('onclick');
        expect($html)->not->toContain('javascript:');
        expect($html)->toContain('x');
    }
});

test('input that isn\'t editor HTML is kept as text and only ever shown escaped', function () {
    // RichText treats a value as HTML only when it starts and ends with a
    // block tag (the editor's output always does). Anything else is plain
    // text: stored as sent, and escaped on display, so it never runs.
    $response = putSubtaskDescription($this, '<p>x</p><script>alert(1)</script>')->assertOk();

    expect($response->json('description_html'))->not->toContain('<script')->toContain('&lt;script&gt;');

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect($page)->not->toContain('<script>alert(1)</script>');
});

test('a blank editor clears the description', function () {
    $this->subtask->update(['description' => '<p>Something</p>']);

    $response = putSubtaskDescription($this, '<p></p>')->assertOk();

    expect($this->subtask->fresh()->description)->toBeNull();
    expect($response->json('description_html'))->toBe('');
});

test('plain text is stored as it is and shown escaped, with its line breaks', function () {
    putSubtaskDescription($this, "line1 <b>not bold</b>\nline2")->assertOk();

    expect($this->subtask->fresh()->description)->toBe("line1 <b>not bold</b>\nline2");

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect($page)->toContain('line1 &lt;b&gt;not bold&lt;/b&gt;<br');
    expect($page)->not->toContain('<b>not bold</b>');
});

test('a description over the limit is refused and nothing changes', function () {
    $this->subtask->update(['description' => '<p>Keep me</p>']);

    putSubtaskDescription($this, '<p>'.str_repeat('a', Subtask::MAX_DESCRIPTION_LENGTH).'</p>')
        ->assertStatus(422)
        ->assertJsonPath('errors.description.0', Subtask::TOO_LONG_MESSAGE);

    expect($this->subtask->fresh()->description)->toBe('<p>Keep me</p>');
});

test('a link chip is kept after sanitizing', function () {
    $chip = '<p><link-preview href="https://example.com" domain="example.com">Example</link-preview></p>';

    $response = putSubtaskDescription($this, $chip)->assertOk();

    expect($this->subtask->fresh()->description)->toContain('<link-preview href="https://example.com" domain="example.com">Example</link-preview>');
    expect($response->json('description_html'))->toContain('<link-preview');
});

test('adding a subtask with a description sanitizes it and returns safe HTML; too long is refused', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/subtasks", [
        'title' => 'New',
        'description' => '<p><em>Hello</em></p><script>alert(1)</script><p>World</p>',
    ])->assertCreated();

    $subtask = Subtask::where('title', 'New')->firstOrFail();
    expect($subtask->description)->toContain('<em>Hello</em>')->not->toContain('<script');
    expect($response->json('description_html'))->toContain('<em>Hello</em>')->not->toContain('<script');

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/subtasks", [
        'title' => 'Too long',
        'description' => str_repeat('a', Subtask::MAX_DESCRIPTION_LENGTH + 1),
    ])->assertStatus(422)->assertJsonPath('errors.description.0', Subtask::TOO_LONG_MESSAGE);
    expect(Subtask::where('title', 'Too long')->exists())->toBeFalse();
});

test('the Add Task form sanitizes subtask descriptions too, and caps them', function () {
    $payload = fn (string $description) => [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'With subtasks',
        'priority' => 'medium',
        'status' => 'pending',
        'subtasks' => [['title' => 'Sub one', 'assignee_id' => '', 'due_date' => '', 'description' => $description]],
    ];

    $this->actingAs($this->management)->post('/tasks', $payload('<p>Fine</p><script>alert(1)</script><p>Done</p>'))->assertSessionHasNoErrors();
    $stored = Subtask::where('title', 'Sub one')->firstOrFail()->description;
    expect($stored)->toContain('Fine')->not->toContain('<script');

    $this->actingAs($this->management)->post('/tasks', $payload(str_repeat('a', Subtask::MAX_DESCRIPTION_LENGTH + 1)))
        ->assertSessionHasErrors(['subtasks.0.description' => Subtask::TOO_LONG_MESSAGE]);
});

test('the task page shows the rich description, with the pencil only for someone who can edit', function () {
    $this->subtask->update(['description' => '<p>See <a href="https://example.com">the brief</a></p>']);

    $editorPage = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect($editorPage)->toMatch('/<a [^>]*href="https:\/\/example\.com"[^>]*>the brief<\/a>/');
    expect(subtaskPencilCount($editorPage))->toBe(1);

    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);
    $this->project->clients()->attach($client->id);

    $viewerPage = $this->actingAs($client)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect($viewerPage)->toMatch('/<a [^>]*href="https:\/\/example\.com"[^>]*>the brief<\/a>/');
    expect(subtaskPencilCount($viewerPage))->toBe(0);
});

test('the length cap fits the TEXT column at 4 bytes a character', function () {
    expect(Subtask::MAX_DESCRIPTION_LENGTH * 4)->toBeLessThanOrEqual(65_535);
});
