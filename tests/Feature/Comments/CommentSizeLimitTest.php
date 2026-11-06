<?php

use App\Models\AccessPermission;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionedInCommentNotification;
use App\Support\RichText;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Notification;

/**
 * task #122: no comment size limit a person would notice. One safety cap
 * (Comment::MAX_BODY_LENGTH, 200,000 characters of stored HTML) against
 * crafted requests, checked on the raw request and on the cleaned HTML
 * with the same number and the same message.
 */
beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->first()->id]);

    $this->task = Task::create([
        'organization_id' => $this->org->id, 'project_id' => $this->project->id, 'department_id' => $this->dept->id,
        'title' => 'Ship it', 'description' => 'd', 'priority' => 'medium', 'status' => 'pending',
    ]);
});

/** A paragraph of $length characters in all (the "<p></p>" included). */
function paragraphOfLength(int $length, string $character = 'a'): string
{
    return '<p>'.str_repeat($character, $length - 7).'</p>';
}

function postComment(object $context, string $body, array $extra = [])
{
    return $context->actingAs($context->management)
        ->postJson("/tasks/{$context->task->id}/comments", ['body' => $body, ...$extra]);
}

test('a comment exactly at the cap saves; one character over is refused with the message, on post and on edit', function () {
    $atCap = paragraphOfLength(Comment::MAX_BODY_LENGTH);
    $overCap = paragraphOfLength(Comment::MAX_BODY_LENGTH + 1);

    $created = postComment($this, $atCap)->assertCreated();
    $comment = Comment::findOrFail($created->json('comment.id'));
    expect($comment->body)->toBe($atCap);

    postComment($this, $overCap)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body' => Comment::TOO_LONG_MESSAGE]);

    $this->actingAs($this->management)->putJson("/comments/{$comment->id}", ['body' => paragraphOfLength(Comment::MAX_BODY_LENGTH, 'b')])->assertOk();
    $this->actingAs($this->management)->putJson("/comments/{$comment->id}", ['body' => $overCap])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body' => Comment::TOO_LONG_MESSAGE]);

    expect(Comment::count())->toBe(1)
        ->and($comment->fresh()->body)->toBe(paragraphOfLength(Comment::MAX_BODY_LENGTH, 'b'));
});

test('the raw-request rule and the cleaned-HTML check use the same cap and the same message', function () {
    // Editor HTML that cleaning leaves unchanged: both checks see the same
    // length, so at the cap both accept and one over both refuse.
    postComment($this, paragraphOfLength(Comment::MAX_BODY_LENGTH))->assertCreated();
    postComment($this, paragraphOfLength(Comment::MAX_BODY_LENGTH + 1))
        ->assertJsonValidationErrors(['body' => Comment::TOO_LONG_MESSAGE]);

    // Cleaning can make a body longer (every link gets rel="noopener
    // noreferrer nofollow"): under the cap as sent, over it once cleaned.
    // cleanBody() refuses it, with the same message.
    $link = '<a href="https://example.com/x">x</a>';
    $links = '<p>'.str_repeat($link, intdiv(Comment::MAX_BODY_LENGTH - 7, mb_strlen($link))).'</p>';
    expect(mb_strlen($links))->toBeLessThanOrEqual(Comment::MAX_BODY_LENGTH);
    postComment($this, $links)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body' => Comment::TOO_LONG_MESSAGE]);

    expect(Comment::count())->toBe(1);
});

test('an empty comment is still refused, on post and on edit', function (string $empty) {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => '<p>keep</p>']);

    postComment($this, $empty)
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body' => 'The body field is required.']);
    $this->actingAs($this->management)->putJson("/comments/{$comment->id}", ['body' => $empty])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['body' => 'The body field is required.']);

    expect(Comment::count())->toBe(1)->and($comment->fresh()->body)->toBe('<p>keep</p>');
})->with(['an empty editor' => ['<p></p>'], 'spaces' => ['   '], 'an editor with spaces' => ['<p>   </p>']]);

test('a long comment with a mention saves the mention and notifies once; editing it keeps the mention', function () {
    Notification::fake();
    $mentioned = User::factory()->create(['name' => 'Mentioned Person']);
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $mentioned->id, 'role_id' => Role::where('slug', 'staff')->first()->id]);
    AccessPermission::create(['user_id' => $mentioned->id, 'organization_id' => $this->org->id, 'department_id' => $this->dept->id, 'allowed' => true]);
    $this->project->staff()->attach($mentioned->id);

    $body = '<p><strong>@Mentioned Person</strong> '.str_repeat('long text ', 5000).'</p>';
    $created = postComment($this, $body, ['mentioned_user_ids' => [$mentioned->id]])->assertCreated();
    $comment = Comment::findOrFail($created->json('comment.id'));

    expect($comment->mentionedUsers()->pluck('users.id')->all())->toBe([$mentioned->id]);
    Notification::assertSentToTimes($mentioned, MentionedInCommentNotification::class, 1);

    $this->actingAs($this->management)->putJson("/comments/{$comment->id}", [
        'body' => $body.'<p>'.str_repeat('more text ', 5000).'</p>',
        'mentioned_user_ids' => [$mentioned->id],
    ])->assertOk();

    expect($comment->fresh()->mentionedUsers()->pluck('users.id')->all())->toBe([$mentioned->id]);
    // Still mentioned once: the edit doesn't notify them again.
    Notification::assertSentToTimes($mentioned, MentionedInCommentNotification::class, 1);
});

test('a long reply saves under its parent', function () {
    $parent = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => '<p>Parent</p>']);
    $reply = paragraphOfLength(50000);

    $created = postComment($this, $reply, ['parent_comment_id' => $parent->id])->assertCreated();

    $stored = Comment::findOrFail($created->json('comment.id'));
    expect($stored->parent_comment_id)->toBe($parent->id)
        ->and($stored->body)->toBe($reply);
});

test('a request too large for PHP is a 413, which the comment box shows as "too long"', function () {
    // The comment box maps any 413 (Laravel's below, or the web server's own
    // HTML 413 page) to the message, before reading the body.
    $page = $this->actingAs($this->management)->get(route('tasks.edit', $this->task))->getContent();
    expect($page)->toContain('response.status === 413')
        ->toContain('This comment is too long to save.');

    // Laravel's ValidatePostSize answers a body over post_max_size with a
    // 413 (PostTooLargeException), before the controller runs. (Last: the
    // server variable stays set for any later request in this test.)
    $this->actingAs($this->management)
        ->withServerVariables(['CONTENT_LENGTH' => (string) (PHP_INT_MAX >> 1)])
        ->postJson("/tasks/{$this->task->id}/comments", ['body' => '<p>hello</p>'])
        ->assertStatus(413);
    expect(Comment::count())->toBe(0);
});

test('every rich-text size cap fits inside the sanitizer\'s byte limit, at up to 4 bytes per character', function () {
    // Over RichText::MAX_INPUT_BYTES the sanitizer returns an empty string,
    // so a cap above it would make a long comment (or description) vanish.
    expect(Comment::MAX_BODY_LENGTH * 4)->toBeLessThanOrEqual(RichText::MAX_INPUT_BYTES)
        // The task description's cap (StoreTaskRequest/UpdateTaskRequest).
        ->and(200_000 * 4)->toBeLessThanOrEqual(RichText::MAX_INPUT_BYTES);

    // And the largest emoji comment the cap allows really survives cleaning.
    $emoji = '<p>'.str_repeat('😀', Comment::MAX_BODY_LENGTH - 7).'</p>';
    expect(RichText::normalize($emoji))->toBe($emoji);
});
