<?php

use App\Models\AuditLog;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionedInCommentNotification;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
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
        'department_id' => $dept->id,
        'title' => 'Ship it',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

test('a comment saved through the editor keeps its formatting and renders it in the task list drilldown', function () {
    $html = '<p>Deploy <strong>after</strong> the <em>review</em> — see <a href="https://example.com/runbook">the runbook</a></p>'
        .'<ol><li><p>build</p></li><li><p>ship</p></li></ol>'
        .'<pre><code class="language-js">run();</code></pre>';

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $html]);

    $response->assertCreated();
    $response->assertJsonPath('comment.body_html', fn ($rendered) => str_contains($rendered, '<strong>after</strong>')
        && str_contains($rendered, '<pre><code class="language-js">run();</code></pre>'));

    $stored = Comment::firstOrFail()->body;
    expect($stored)
        ->toContain('<strong>after</strong>')
        ->toContain('<em>review</em>')
        ->toContain('<ol><li><p>build</p></li>')
        ->toContain('href="https://example.com/runbook"')
        ->toContain('rel="noopener noreferrer nofollow"');

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee('<strong>after</strong>', false)
        ->assertSee('<em>review</em>', false)
        ->assertSee('<pre><code class="language-js">run();</code></pre>', false)
        ->assertSee('comment-body-text', false);

    // The same comment on the Task edit page, which lists comments too.
    $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()
        ->assertSee('<strong>after</strong>', false);
});

test('an existing plain-text comment renders as escaped text, with its line breaks, in the drilldown and the polling endpoint', function () {
    $legacy = "Looks good <b>to me</b> & ready\nsecond line";
    Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => $legacy]);

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertSee('Looks good &lt;b&gt;to me&lt;/b&gt; &amp; ready<br>', false)
        ->assertDontSee('<b>to me</b>', false);

    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")
        ->assertOk()
        ->assertJsonPath('comments.0.body', $legacy)
        ->assertJsonPath('comments.0.body_html', '<p>Looks good &lt;b&gt;to me&lt;/b&gt; &amp; ready<br>second line</p>');

    // Viewing never rewrites it.
    expect(Comment::firstOrFail()->body)->toBe($legacy);
});

test('a legacy plain-text comment loads into the comment editor when edited, and re-saves as HTML', function () {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => "Old plain\ncomment"]);

    // What the Edit button hands the editor is the rendered body_html.
    $polled = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->json('comments.0');
    $fragment = richTextFragment($polled['body_html']);
    expect($fragment->getElementsByTagName('p')->length)->toBe(1);
    expect($fragment->getElementsByTagName('br')->length)->toBe(1);
    expect($fragment->getElementsByTagName('body')->item(0)->textContent)->toBe('Old plaincomment');

    $update = $this->actingAs($this->management)->putJson("/comments/{$comment->id}", ['body' => '<p>Old plain <strong>edited</strong></p>']);

    $update->assertOk();
    $update->assertJsonPath('comment.body', '<p>Old plain <strong>edited</strong></p>');
    $update->assertJsonPath('comment.body_html', '<p>Old plain <strong>edited</strong></p>');
    expect($update->json('comment.body_hash'))->not->toBe($polled['body_hash']);
    expect($comment->fresh()->body)->toBe('<p>Old plain <strong>edited</strong></p>');
});

test('the polling hash changes only when the body changes', function () {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => '<p>one</p>']);

    $first = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->json('comments.0.body_hash');
    $again = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->json('comments.0.body_hash');
    expect($again)->toBe($first);

    $comment->update(['body' => '<p>two</p>']);
    $changed = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->json('comments.0.body_hash');
    expect($changed)->not->toBe($first);
});

test('script, event handlers, javascript links and images are stripped from a comment', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p onclick="steal()">hi <a href="javascript:alert(1)">bad</a></p><p><img src="x" onerror="alert(1)"><script>alert(1)</script></p>',
    ])->assertCreated();

    $stored = Comment::firstOrFail()->body;
    expect($stored)
        ->not->toContain('onclick')
        ->not->toContain('javascript:')
        ->not->toContain('<img')
        ->not->toContain('<script');

    $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('onerror=', false);
});

test('hostile markup written straight into the database is still neutralized when rendered', function () {
    // Bypasses every save-time check (direct write / bad import / old bug).
    Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $this->management->id,
        'body' => '<p onmouseover="x()">hi</p><p><script>alert(9)</script><img src=x onerror=alert(8)></p>',
    ]);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->org->id}")->assertOk();
    $page->assertDontSee('alert(9)', false);
    $page->assertDontSee('onerror=alert(8)', false);
    $page->assertDontSee('onmouseover', false);

    $json = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->json('comments.0.body_html');
    expect($json)->not->toContain('<script')->not->toContain('onmouseover')->not->toContain('<img');
});

test('the 2000-character limit applies to the visible text, not to the markup around it', function () {
    // 1900 characters of text wrapped in ~30KB of tags: fine.
    $formatted = '<p>'.str_repeat('<strong>a</strong>', 1900).'</p>';
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $formatted])->assertCreated();

    // 2001 characters of visible text: rejected, same as before the editor.
    $tooLong = '<p>'.str_repeat('a', 2001).'</p>';
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $tooLong])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('body');

    expect(Comment::count())->toBe(1);
});

test('an empty editor cannot be posted as a comment', function () {
    foreach (['<p></p>', '', '   ', '<p>   </p>'] as $blank) {
        $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => $blank])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('body');
    }

    expect(Comment::count())->toBe(0);
});

test('an edit that empties a comment is rejected and leaves the original', function () {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => '<p>keep</p>']);

    $this->actingAs($this->management)->putJson("/comments/{$comment->id}", ['body' => '<p></p>'])
        ->assertUnprocessable();

    expect($comment->fresh()->body)->toBe('<p>keep</p>');
});

test('plain-text comment submissions from older clients are still accepted and stored as sent', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", ['body' => 'A < B and C'])->assertCreated();

    expect(Comment::firstOrFail()->body)->toBe('A < B and C');
});

test('mentioning someone in a formatted comment still notifies them', function () {
    Notification::fake();
    $mentioned = User::factory()->create();
    $this->project->staff()->attach($mentioned->id);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p><strong>@'.$mentioned->name.'</strong> please take a look</p>',
        'mentioned_user_ids' => [$mentioned->id],
    ])->assertCreated();

    expect(Comment::firstOrFail()->mentionedUsers()->pluck('users.id')->all())->toBe([$mentioned->id]);
    Notification::assertSentTo($mentioned, MentionedInCommentNotification::class);
});

test('the audit trail shows the words of a formatted comment, not its tags', function () {
    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '<p>Ship <strong>tonight</strong></p>',
    ])->assertCreated();

    $audit = AuditLog::where('action', 'comment.created')->firstOrFail();

    expect($audit->describeChanges())->toContain('Ship tonight')->not->toContain('<strong>');
    expect($audit->entityLabel())->toBe('Ship tonight');
});
