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

/**
 * Visual @mention highlighting (task #70, phase 3) — matching Jira: every
 * rendered mention gets a base "pill" treatment, and a mention of the
 * CURRENTLY LOGGED-IN viewer specifically gets an additional, more
 * prominent one. There's no real TipTap Mention node behind a mention
 * (same finding as phases 1/2) — it's plain "@Full Name" text — so the
 * actual highlighting is a client-side DOM transform
 * (resources/js/mention-highlight.js) driven by two pieces of data this
 * suite verifies at the HTTP/render level, since Pest can't execute that
 * JS itself:
 *
 * 1. data-mentioned-users (Blade-rendered pages) / mentioned_users (JSON
 *    endpoints) — the durable, permission-checked list of who's actually
 *    mentioned in a given comment (Comment::mentionedUsers(), the same
 *    pivot syncMentions() already maintained before this phase), which
 *    mention-highlight.js searches a body's text for. Identical in shape
 *    whether the mention text got there by typing or via Phase 2's
 *    reply-compose pre-fill — nothing about this phase's data
 *    distinguishes the two.
 * 2. the current-user-id meta tag (layouts/authenticated.blade.php) —
 *    what mention-highlight.js compares each mentioned user's id against
 *    to decide the extra "this is you" class. Two different viewers of
 *    the exact same saved comment get the exact same mentioned_users
 *    payload back — proving the highlighting decision is resolved at
 *    render time per viewer, never baked into the stored content itself.
 *
 * What this suite deliberately can't check: that mention-highlight.js
 * actually paints `.mention`/`.mention-you` correctly in a real browser —
 * flagged for your own hands-on check, same as Phase 2's polling-diff/
 * pre-fill behavior was.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create(['name' => 'Mona Manager']);
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->staff = User::factory()->create(['name' => 'Sam Staffer']);
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);
    AccessPermission::create([
        'user_id' => $this->staff->id,
        'organization_id' => $this->org->id,
        'department_id' => $this->dept->id,
        'allowed' => true,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Ship it',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function findCommentCardBody(string $page, int $commentId): ?DOMElement
{
    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();
    $xpath = new DOMXPath($document);

    foreach ($xpath->query('//*[contains(@class, "comment-card")]') as $card) {
        if ($card->getAttribute('data-comment-id') === (string) $commentId) {
            $bodies = $xpath->query('.//*[contains(@class, "comment-body-text")]', $card);

            return $bodies->length > 0 ? $bodies->item(0) : null;
        }
    }

    return null;
}

test('the page exposes a current-user-id meta tag matching whoever is actually logged in', function () {
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();
    $xpath = new DOMXPath($document);

    $meta = $xpath->query('//meta[@name="current-user-id"]')->item(0);
    expect($meta)->not->toBeNull();
    expect($meta->getAttribute('content'))->toBe((string) $this->management->id);

    $pageAsStaff = $this->actingAs($this->staff)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$pageAsStaff);
    $metaAsStaff = (new DOMXPath($document))->query('//meta[@name="current-user-id"]')->item(0);
    expect($metaAsStaff->getAttribute('content'))->toBe((string) $this->staff->id);
});

test('a top-level comment\'s rendered body carries data-mentioned-users listing exactly who\'s actually mentioned', function () {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->staff->id, 'body' => '@Mona Manager can you review this']);
    $comment->mentionedUsers()->sync([$this->management->id]);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $body = findCommentCardBody($page, $comment->id);

    expect($body)->not->toBeNull();
    $mentioned = json_decode($body->getAttribute('data-mentioned-users'), true);
    expect($mentioned)->toBe([['id' => $this->management->id, 'name' => 'Mona Manager']]);
});

test('a reply\'s rendered body carries data-mentioned-users too, identically shaped to a top-level comment\'s — no distinction for an auto-filled vs. manually-typed mention', function () {
    $topLevel = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => 'Original comment']);
    $reply = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->staff->id, 'parent_comment_id' => $topLevel->id, 'body' => '@Mona Manager will do']);
    $reply->mentionedUsers()->sync([$this->management->id]);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $body = findCommentCardBody($page, $reply->id);

    expect($body)->not->toBeNull();
    $mentioned = json_decode($body->getAttribute('data-mentioned-users'), true);
    expect($mentioned)->toBe([['id' => $this->management->id, 'name' => 'Mona Manager']]);
});

test('a comment with no mentions renders an empty data-mentioned-users array, not a missing attribute', function () {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => 'Just a plain comment']);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $body = findCommentCardBody($page, $comment->id);

    expect($body)->not->toBeNull();
    expect(json_decode($body->getAttribute('data-mentioned-users'), true))->toBe([]);
});

test('the polling endpoint returns mentioned_users for every comment, top-level and reply alike', function () {
    $topLevel = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->staff->id, 'body' => '@Mona Manager please check']);
    $topLevel->mentionedUsers()->sync([$this->management->id]);
    $reply = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'parent_comment_id' => $topLevel->id, 'body' => 'On it']);

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->assertOk();
    $comments = collect($response->json('comments'))->keyBy('id');

    expect($comments[$topLevel->id]['mentioned_users'])->toBe([['id' => $this->management->id, 'name' => 'Mona Manager']]);
    expect($comments[$reply->id]['mentioned_users'])->toBe([]);
});

test('posting a comment with a mention returns mentioned_users in the response, ready for the client to highlight immediately', function () {
    $response = $this->actingAs($this->staff)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '@Mona Manager fyi',
        'mentioned_user_ids' => [$this->management->id],
    ])->assertCreated();

    expect($response->json('comment.mentioned_users'))->toBe([['id' => $this->management->id, 'name' => 'Mona Manager']]);
});

test('saving an edited comment returns the updated mentioned_users, not the pre-edit list', function () {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => '@Sam Staffer take a look']);
    $comment->mentionedUsers()->sync([$this->staff->id]);

    $response = $this->actingAs($this->management)->putJson("/comments/{$comment->id}", [
        'body' => 'never mind, ignore',
        'mentioned_user_ids' => [],
    ])->assertOk();

    expect($response->json('comment.mentioned_users'))->toBe([]);
});

test('mentioning an ineligible user is reflected nowhere in mentioned_users, matching the existing eligibility rule', function () {
    $outsider = User::factory()->create(['name' => 'Ollie Outsider']);

    $response = $this->actingAs($this->staff)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'cc @Ollie Outsider',
        'mentioned_user_ids' => [$outsider->id],
    ])->assertCreated();

    expect($response->json('comment.mentioned_users'))->toBe([]);
});

test('two different viewers of the exact same comment see the exact same mentioned_users payload — the "is this you" distinction is resolved client-side, not baked into the saved content', function () {
    $comment = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->staff->id, 'body' => '@Mona Manager please review']);
    $comment->mentionedUsers()->sync([$this->management->id]);

    $asManagement = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->assertOk();
    $asStaff = $this->actingAs($this->staff)->getJson("/tasks/{$this->task->id}/comments")->assertOk();

    $mentionedForManagement = collect($asManagement->json('comments'))->firstWhere('id', $comment->id)['mentioned_users'];
    $mentionedForStaff = collect($asStaff->json('comments'))->firstWhere('id', $comment->id)['mentioned_users'];

    expect($mentionedForManagement)->toBe($mentionedForStaff);
    expect($mentionedForManagement)->toBe([['id' => $this->management->id, 'name' => 'Mona Manager']]);
});
