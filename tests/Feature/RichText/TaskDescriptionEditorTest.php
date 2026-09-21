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
});

function makeTaskWithDescription(?string $description): Task
{
    return Task::create([
        'organization_id' => test()->org->id,
        'project_id' => test()->project->id,
        'department_id' => test()->dept->id,
        'title' => 'A task',
        'description' => $description,
        'priority' => 'medium',
        'status' => 'pending',
    ]);
}

function taskUpdatePayload(Task $task, array $overrides = []): array
{
    return array_merge([
        'project_id' => $task->project_id,
        'department_id' => $task->department_id,
        'title' => $task->title,
        'priority' => 'medium',
        'status' => 'pending',
    ], $overrides);
}

// ---------------------------------------------------------------------------
// Backward compatibility: every description saved before the editor swap is
// plain text. These are the tests standing between that data and a regression.
// ---------------------------------------------------------------------------

test('an existing plain-text description loads into the editor as readable content, not raw escaped markup', function () {
    $legacy = "Fix the login redirect.\nSee <script>alert('x')</script> & the 5 < 6 note.\n\nSecond paragraph: \"quoted\".";
    $task = makeTaskWithDescription($legacy);

    $page = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")->assertOk()->getContent();

    $content = richTextEditorContent($page, 'Description');
    expect($content)->not->toBeNull()->not->toBe('');

    // Valid editor HTML: two paragraphs (blank line = paragraph break), the
    // single newline kept as a <br>, and no element the author never typed.
    $fragment = richTextFragment($content);
    expect($fragment->getElementsByTagName('p')->length)->toBe(2);
    expect($fragment->getElementsByTagName('br')->length)->toBe(1);
    expect($fragment->getElementsByTagName('script')->length)->toBe(0);

    // The characters that would be dangerous as markup arrive as the literal
    // text the author typed — "<script>" shown as text, "&" as "&", "<" as "<".
    $text = $fragment->getElementsByTagName('body')->item(0)->textContent;
    expect($text)->toContain("See <script>alert('x')</script> & the 5 < 6 note.");
    expect($text)->toContain('Second paragraph: "quoted".');

    // Not double-escaped: the editor must never show "&lt;" to a user.
    expect($content)->not->toContain('&amp;lt;')->not->toContain('&amp;amp;');
});

test('the hidden form field starts with the same converted content, so saving an untouched legacy description is lossless', function () {
    $task = makeTaskWithDescription("Line one\nLine two");

    $page = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")->getContent();

    expect(richTextHiddenInputValue($page, 'Description'))->toBe(richTextEditorContent($page, 'Description'));

    // Round trip: submit exactly what the page hands the editor.
    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", taskUpdatePayload($task, ['description' => richTextHiddenInputValue($page, 'Description')]))
        ->assertRedirect();

    $reloaded = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")->getContent();
    $fragment = richTextFragment(richTextEditorContent($reloaded, 'Description'));
    expect($fragment->getElementsByTagName('body')->item(0)->textContent)->toBe('Line oneLine two');
    expect($fragment->getElementsByTagName('br')->length)->toBe(1);
});

test('opening a legacy plain-text task never rewrites the stored value', function () {
    $legacy = "Untouched\nplain text <b>as typed</b>";
    $task = makeTaskWithDescription($legacy);

    $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")->assertOk();
    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk();

    expect($task->fresh()->description)->toBe($legacy);
});

test('a task with no description, or only whitespace, opens with an empty editor', function () {
    foreach ([null, '', "  \n "] as $blank) {
        $task = makeTaskWithDescription($blank);

        $page = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")->assertOk()->getContent();

        expect(richTextEditorContent($page, 'Description'))->toBe('');
    }
});

test('legacy plain text shows as escaped text in the task list drilldown and the read-only task view', function () {
    $task = makeTaskWithDescription("Plain <i>text</i> & more\nsecond line");

    $list = $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk();
    $list->assertSee('Plain &lt;i&gt;text&lt;/i&gt; &amp; more<br>', false);
    $list->assertDontSee('<i>text</i>', false);

    $client = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $client->id, 'role_id' => Role::where('slug', 'client')->first()->id]);
    $this->project->clients()->attach($client->id);

    $this->actingAs($client)->get("/tasks/{$task->id}/edit")->assertOk()
        ->assertSee('Plain &lt;i&gt;text&lt;/i&gt; &amp; more<br>', false)
        ->assertDontSee('<i>text</i>', false);
});

// ---------------------------------------------------------------------------
// New content through the editor: stored as HTML, sanitized, round-trips.
// ---------------------------------------------------------------------------

test('a description saved from the editor with bold, a link and a code block persists and re-renders on reload', function () {
    $task = makeTaskWithDescription('old plain text');

    $html = '<p>Intro <strong>bold</strong>, <em>italic</em>, <u>underlined</u> and <a href="https://example.com/docs">a link</a></p>'
        .'<h2>Steps</h2><ul><li><p>one</p></li><li><p>two</p></li></ul>'
        .'<pre><code class="language-php">echo 42;</code></pre>';

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", taskUpdatePayload($task, ['description' => $html]))
        ->assertRedirect();

    $stored = $task->fresh()->description;
    expect($stored)
        ->toContain('<strong>bold</strong>')
        ->toContain('<em>italic</em>')
        ->toContain('<u>underlined</u>')
        ->toContain('<h2>Steps</h2>')
        ->toContain('<ul><li><p>one</p></li>')
        ->toContain('<pre><code class="language-php">echo 42;</code></pre>')
        ->toContain('href="https://example.com/docs"')
        // Outbound links must not hand the opener page to the target.
        ->toContain('rel="noopener noreferrer nofollow"')
        ->toContain('target="_blank"');

    // Reload in the editor: the saved HTML is what the editor is given.
    $page = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")->getContent();
    $fragment = richTextFragment(richTextEditorContent($page, 'Description'));
    expect($fragment->getElementsByTagName('strong')->length)->toBe(1);
    expect($fragment->getElementsByTagName('a')->item(0)->getAttribute('href'))->toBe('https://example.com/docs');
    expect($fragment->getElementsByTagName('pre')->length)->toBe(1);
    expect($fragment->getElementsByTagName('code')->item(0)->getAttribute('class'))->toBe('language-php');
    expect($fragment->getElementsByTagName('li')->length)->toBe(2);

    // And read-only, in the task list drilldown, as real markup (not escaped).
    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")
        ->assertSee('<strong>bold</strong>', false)
        ->assertSee('<pre><code class="language-php">echo 42;</code></pre>', false)
        ->assertSee('data-rich-text-content', false);
});

test('creating a task with a formatted description stores sanitized HTML', function () {
    $this->actingAs($this->management)->post('/tasks', [
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Formatted on create',
        'description' => '<p>Hello <strong>team</strong></p>',
        'priority' => 'high',
        'status' => 'pending',
    ])->assertRedirect();

    expect(Task::where('title', 'Formatted on create')->firstOrFail()->description)->toBe('<p>Hello <strong>team</strong></p>');
});

test('the editor page for a formatted description is not double-escaped either', function () {
    $task = makeTaskWithDescription('<p>Hello <strong>team</strong> &amp; friends</p>');

    $page = $this->actingAs($this->management)->get("/tasks/{$task->id}/edit")->getContent();

    expect(richTextEditorContent($page, 'Description'))->toBe('<p>Hello <strong>team</strong> &amp; friends</p>');
});

test('script, event handlers, javascript links, images and arbitrary classes are stripped from a submitted description', function () {
    $task = makeTaskWithDescription(null);

    $hostile = '<p onclick="steal()">hi <a href="javascript:alert(1)">bad</a> <a href="https://ok.example" onmouseover="x()">ok</a></p>'
        .'<img src="x" onerror="alert(1)">'
        .'<pre><code class="fixed inset-0 z-50 bg-white">x</code></pre>'
        .'<script>alert(1)</script>';
    // Ends in a non-block element, so this exact string isn't editor output
    // and is kept as plain text — the render path must escape it, not run it.
    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", taskUpdatePayload($task, ['description' => $hostile]))
        ->assertRedirect();

    $list = $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk();
    $list->assertDontSee('<script>alert(1)</script>', false);
    $list->assertDontSee('<img src="x"', false);
    $list->assertDontSee('onclick="steal()"', false);

    // The editor-shaped variant (block elements only) is sanitized on save.
    $shaped = '<p onclick="steal()">hi <a href="javascript:alert(1)">bad</a> <a href="https://ok.example" onmouseover="x()">ok</a></p>'
        .'<pre><code class="fixed inset-0 z-50 bg-white">x</code></pre><p><img src="x" onerror="alert(1)"><script>alert(1)</script></p>';
    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", taskUpdatePayload($task, ['description' => $shaped]))
        ->assertRedirect();

    $stored = $task->fresh()->description;
    expect($stored)
        ->not->toContain('onclick')
        ->not->toContain('onmouseover')
        ->not->toContain('javascript:')
        ->not->toContain('<img')
        ->not->toContain('<script')
        ->not->toContain('fixed inset-0')
        ->toContain('href="https://ok.example"');
});

test('an empty editor saves as no description', function () {
    $task = makeTaskWithDescription('something');

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", taskUpdatePayload($task, ['description' => '<p></p>']))
        ->assertRedirect();

    // update() has always stored a blank description as '' (not null).
    expect($task->fresh()->description)->toBeEmpty();
});

test('a plain-text description submitted directly (imports, older clients) is stored exactly as sent', function () {
    $task = makeTaskWithDescription(null);

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", taskUpdatePayload($task, ['description' => "a < b\nsecond line"]))
        ->assertRedirect();

    expect($task->fresh()->description)->toBe("a < b\nsecond line");
});

test('a malformed non-string description is a validation error, not a server error', function () {
    $task = makeTaskWithDescription('x');

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", taskUpdatePayload($task, ['description' => ['not', 'a string']]))
        ->assertSessionHasErrors('description');
});

test('a very long description is kept whole rather than silently emptied by the sanitizer', function () {
    $task = makeTaskWithDescription(null);
    $paragraph = str_repeat('word ', 12000); // ~60KB, well past the sanitizer library default of 20KB

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", taskUpdatePayload($task, ['description' => "<p>{$paragraph}</p>"]))
        ->assertRedirect();

    expect(strlen($task->fresh()->description))->toBeGreaterThan(60000);
});
