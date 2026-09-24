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

// The editor stores emoji as plain Unicode characters inside the same HTML
// the rest of the rich text uses (the browser-side serializer unwraps the
// TipTap emoji node's <span>), so there is no schema change and nothing
// special about them on the server — these tests pin that assumption down,
// including the awkward multi-codepoint kinds (skin tones, ZWJ families, flags).

const EMOJI_SAMPLE = 'Ship it 🚀 thanks 👍🏽 team 👨‍👩‍👧 from 🇮🇩 ❤️';

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
        'title' => 'Emoji task',
        'description' => 'old plain text',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function emojiTaskPayload(Task $task, string $description): array
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

test('a description saved with emoji persists byte-for-byte and re-renders in the editor and the drilldown', function () {
    $html = '<p>'.EMOJI_SAMPLE.'</p>';

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", emojiTaskPayload($this->task, $html))->assertRedirect();

    // Stored as the bare characters: no wrapper element, no entity encoding.
    expect($this->task->fresh()->description)->toBe($html);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect(descriptionViewContent($page))->toBe($html);
    expect(descriptionFallbackInputValue($page))->toBe($html);

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()->assertSee($html, false);
});

test('an emoji saved next to formatting keeps both', function () {
    $html = '<p>Great <strong>work 🎉</strong> on <a href="https://example.com">the launch</a> 🚀</p><ul><li><p>✅ done</p></li></ul>';

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", emojiTaskPayload($this->task, $html))->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)->toContain('<strong>work 🎉</strong>')->toContain('launch</a> 🚀</p>')->toContain('<li><p>✅ done</p></li>');
});

test('the editor node markup an emoji serializes to is reduced to the bare character on the server too', function () {
    // The browser unwraps this before submitting; this is the safety net for
    // any client that doesn't (older cached JS, a crafted request).
    $wrapped = '<p>Ship <span data-name="rocket" data-type="emoji">🚀</span> now</p>';

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", emojiTaskPayload($this->task, $wrapped))->assertRedirect();

    expect($this->task->fresh()->description)->toBe('<p>Ship 🚀 now</p>');
});

test('the emoji span wrapper never survives a submission, with or without the image-support sanitizer allowlist (task #4 phase 3)', function () {
    // With the extension's stock data, an emoji the browser can't detect
    // renders as a cdn.jsdelivr.net <img> instead of the character — the
    // editor strips that fallback client-side (see the static test below).
    // Server-side, the emoji <span> wrapper itself is still never allowed to
    // survive (unwrapped to its content, same as any other unrecognized
    // span) — what changed once phase 3 allowed <img src> generally is that
    // the *inner* image tag it wrapped is no longer stripped merely for
    // being an <img>; an absolute https image URL is legitimate content
    // either way, uploaded or not, so this is expected, not a regression.
    $withImage = '<p>hi <span data-name="grinning" data-type="emoji"><img src="https://cdn.jsdelivr.net/npm/emoji-datasource-apple/img/apple/64/1f600.png" alt="grinning emoji"></span> there</p>';

    $this->actingAs($this->management)->put("/tasks/{$this->task->id}", emojiTaskPayload($this->task, $withImage))->assertRedirect();

    $stored = $this->task->fresh()->description;
    expect($stored)
        ->not->toContain('<span')
        ->not->toContain('data-name')
        ->not->toContain('data-type')
        ->toContain('<img src="https://cdn.jsdelivr.net/npm/emoji-datasource-apple/img/apple/64/1f600.png"');
});

test('a comment with emoji persists and renders formatted in the task list drilldown and the polling endpoint', function () {
    $html = '<p>Nice one <strong>team</strong> 👍🏽🎉 '.'👨‍👩‍👧'.'</p>';

    $created = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $html]);

    $created->assertCreated()->assertJsonPath('comment.body_html', $html);
    expect(Comment::firstOrFail()->body)->toBe($html);

    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")
        ->assertOk()
        ->assertJsonPath('comments.0.body_html', $html);

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()->assertSee($html, false);
    $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->assertSee($html, false);
});

test('editing a comment to add an emoji keeps it', function () {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => 'plain old comment']);

    $this->actingAs($this->management)->putJson("/comments/{$comment->id}", ['body' => '<p>plain old comment 😀</p>'])
        ->assertOk()
        ->assertJsonPath('comment.body_html', '<p>plain old comment 😀</p>');

    expect($comment->fresh()->body)->toBe('<p>plain old comment 😀</p>');
});

test('emoji count as one character each toward the comment length limit', function () {
    $atLimit = '<p>'.str_repeat('😀', 2000).'</p>';
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $atLimit])->assertCreated();

    $overLimit = '<p>'.str_repeat('😀', 2001).'</p>';
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $overLimit])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('body');

    expect(Comment::count())->toBe(1);
    expect(Comment::firstOrFail()->body)->toBe($atLimit);
});

test('an emoji-only comment counts as content, not as empty', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => '<p>👍</p>'])->assertCreated();

    expect(Comment::firstOrFail()->body)->toBe('<p>👍</p>');
});

test('existing plain-text content is untouched by emoji support', function () {
    $legacy = "Plain old description\nwith a colon: like this, and 10:30 too";
    $this->task->update(['description' => $legacy]);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    // Loaded as ordinary text — nothing that looks like :shortcode: is turned into an emoji server-side.
    expect(richTextFragment(descriptionViewContent($page))->getElementsByTagName('body')->item(0)->textContent)
        ->toBe('Plain old descriptionwith a colon: like this, and 10:30 too');
    expect($this->task->fresh()->description)->toBe($legacy);
});

test('the MySQL connection defaults to utf8mb4 so four-byte emoji need no schema change', function () {
    expect(config('database.connections.mysql.charset'))->toBe('utf8mb4');
    expect(config('database.connections.mysql.collation'))->toBe('utf8mb4_unicode_ci');
    expect(config('database.connections.mariadb.charset'))->toBe('utf8mb4');
});

// ---------------------------------------------------------------------------
// The editor's JavaScript can't be executed by the Pest suite (no JS test
// runner in this project). These pin the wiring that makes emoji work in BOTH
// editors, so a refactor can't silently drop it; the behavior itself was
// exercised in a real browser.
// ---------------------------------------------------------------------------

test('the Description editor and the Comment editor are the same shared component', function () {
    // Description is a permanently-live <x-rich-text-editor> root (task
    // #4, description autosave), the identical component the New comment
    // box already used — both present immediately, no click needed to
    // construct either one.
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    expect(richTextEditorNode($page, 'New comment'))->not->toBeNull();
    expect(richTextEditorNode($page, 'Description'))->not->toBeNull();

    // ...and that module is the only place an editor is ever constructed, so an
    // extension added there reaches every usage.
    $constructors = collect(glob(resource_path('js/*.js')))
        ->filter(fn ($file) => str_contains(file_get_contents($file), 'new Editor('))
        ->map(fn ($file) => basename($file))
        ->values()
        ->all();
    expect($constructors)->toBe(['rich-text-editor.js']);

    expect(file_get_contents(resource_path('views/tasks/_comments.blade.php')))->not->toContain('Emoji');
});

test('the shared editor registers the emoji extension with native-only rendering and an autocomplete', function () {
    $source = file_get_contents(resource_path('js/rich-text-editor.js'));

    expect($source)
        ->toContain("from '@tiptap/extension-emoji'")
        ->toContain('Emoji.configure(')
        // No image set: the bundled fallback PNG URLs are stripped from the data...
        ->toContain('delete copy.fallbackImage')
        ->toContain('emojis: NATIVE_EMOJIS')
        // ...typed emoticons like ":)" are left as text...
        ->toContain('enableEmoticons: false')
        // ...and the ":" autocomplete is supplied (the extension ships none).
        ->toContain('items: function (props) { return searchEmojis(props.editor, props.query); }')
        ->toContain('render: emojiSuggestionRenderer');

    // Emoji nodes are unwrapped to the plain character before the HTML is saved.
    expect($source)->toContain('data-type="emoji"');
});

test('the toolbar has an emoji picker button directly after the code block button, in the shared editor', function () {
    $source = file_get_contents(resource_path('js/rich-text-editor.js'));

    // The TOOLBAR array is the one list of buttons every editor gets.
    // task #4 phase 3 (image upload) appends an "image" button after emoji —
    // this only pins that emoji itself still comes directly after codeBlock,
    // not that it's the very last button.
    preg_match('/const TOOLBAR = \[(.*?)\n\];/s', $source, $block);
    preg_match_all("/key: '(\w+)'/", $block[1] ?? '', $keys);

    $codeBlockIndex = array_search('codeBlock', $keys[1], true);
    expect($keys[1][$codeBlockIndex + 1] ?? null)->toBe('emoji');

    // It opens a picker that inserts through the extension's own command (so
    // bold/italic around the cursor carry over) and only from the native pool.
    expect($source)
        ->toContain('function buildEmojiPicker(editor)')
        ->toContain('.setEmoji(item.name)')
        ->toContain('availableEmojis(editor)')
        ->toContain('emoji: function (button) { emojiPicker.toggle(button); }');
});

test('the committed production build contains the emoji extension in the editor chunk', function () {
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $entry = $manifest['resources/js/rich-text-editor.js'] ?? null;

    expect($entry)->not->toBeNull();
    // The extension's suggestion plugin key is a stable string in its compiled code.
    expect(file_get_contents(public_path('build/'.$entry['file'])))->toContain('emojiSuggestion');
});
