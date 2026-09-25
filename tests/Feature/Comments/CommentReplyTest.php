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
use Illuminate\Support\Facades\Notification;

/**
 * Threaded replies on Task comments (task #70, phase 2), matching Jira
 * Cloud's real behavior: every comment — top-level or itself a reply —
 * shows a "Reply" action, but storage stays genuinely flat/one-level
 * regardless of which one was clicked (CommentController::resolveReply()
 * always re-parents to the top-level ancestor). Replying auto-inserts a
 * real @mention of whoever's comment was actually clicked, reusing the
 * EXISTING mention/notification pipeline (MentionedInCommentNotification)
 * rather than a separate notification type.
 */
beforeEach(function () {
    $this->owner = createOwner();

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
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    $this->topLevel = Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $this->management->id,
        'body' => 'Original comment',
    ]);
});

function makeEligibleStaff(Organization $org, Department $department): User
{
    $staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);
    AccessPermission::create([
        'user_id' => $staff->id,
        'organization_id' => $org->id,
        'department_id' => $department->id,
        'allowed' => true,
    ]);

    return $staff;
}

test('replying to a top-level comment creates a reply with parent_comment_id pointing at it', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'A reply',
        'parent_comment_id' => $this->topLevel->id,
    ]);

    $response->assertCreated()->assertJsonPath('comment.parent_comment_id', $this->topLevel->id);

    $reply = Comment::where('body', 'like', '%A reply%')->firstOrFail();
    expect($reply->parent_comment_id)->toBe($this->topLevel->id);
});

test('replying to an existing reply re-parents the new comment to the ORIGINAL top-level comment, not the reply that was clicked', function () {
    $replier = makeEligibleStaff($this->org, $this->dept);
    $firstReply = Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $replier->id,
        'parent_comment_id' => $this->topLevel->id,
        'body' => 'First reply',
    ]);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Replying to the reply',
        'parent_comment_id' => $firstReply->id,
    ]);

    $response->assertCreated();
    // Stored value points at the TOP-LEVEL comment, never at $firstReply's
    // own id - confirms the flat re-parenting, and doubles as the "never
    // left pointing at a non-top-level comment" invariant check.
    $response->assertJsonPath('comment.parent_comment_id', $this->topLevel->id);
    expect($response->json('comment.parent_comment_id'))->not->toBe($firstReply->id);

    $secondReply = Comment::where('body', 'like', '%Replying to the reply%')->firstOrFail();
    expect($secondReply->parent_comment_id)->toBe($this->topLevel->id);

    // Both replies end up flat, siblings under the same top-level thread.
    expect($this->topLevel->fresh()->replies()->pluck('id')->sort()->values()->all())
        ->toBe(collect([$firstReply->id, $secondReply->id])->sort()->values()->all());
});

test('a parent_comment_id from a different task is rejected', function () {
    $otherTask = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'A different task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    $otherTaskComment = Comment::create([
        'task_id' => $otherTask->id,
        'user_id' => $this->management->id,
        'body' => 'On the other task',
    ]);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Cross-task reply attempt',
        'parent_comment_id' => $otherTaskComment->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('parent_comment_id');
});

test('management, a staff member with department access, and the project\'s client can all reply, matching CommentPolicy', function () {
    $staff = makeEligibleStaff($this->org, $this->dept);

    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $this->project->clients()->attach($client->id);

    foreach ([$this->management, $staff, $client] as $user) {
        $this->actingAs($user)->postJson("/tasks/{$this->task->id}/comments", [
            'body' => 'Reply from '.$user->id,
            'parent_comment_id' => $this->topLevel->id,
        ])->assertCreated();
    }

    expect($this->topLevel->fresh()->replies()->count())->toBe(3);
});

test('a staff member without department access to the task cannot reply', function () {
    $otherDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Sales', 'color' => '#000000']);
    $staffWithoutAccess = makeEligibleStaff($this->org, $otherDept);

    $this->actingAs($staffWithoutAccess)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Should not work',
        'parent_comment_id' => $this->topLevel->id,
    ])->assertForbidden();

    expect($this->topLevel->fresh()->replies()->count())->toBe(0);
});

test('replying to someone\'s comment auto-inserts a real mention of that comment\'s author, which triggers the existing mention notification', function () {
    Notification::fake();
    $staff = makeEligibleStaff($this->org, $this->dept);

    $response = $this->actingAs($staff)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Sounds good',
        'parent_comment_id' => $this->topLevel->id,
    ]);

    $response->assertCreated();

    // The auto-mention is REAL content in the stored body, not a
    // side-channel notification payload — it uses the exact same
    // "@FullName" plain-text convention a manually-typed mention does
    // (this editor has no special mention node/markup — see
    // RichText's own sanitizer allowlist).
    $reply = Comment::where('body', 'like', '%Sounds good%')->firstOrFail();
    expect($reply->body)->toContain('@'.$this->management->name);

    // And it's recorded as a real mention (the pivot), notified via the
    // existing MentionedInCommentNotification - no separate notification
    // type.
    expect($reply->mentionedUsers()->pluck('users.id')->all())->toBe([$this->management->id]);
    Notification::assertSentTo($this->management, MentionedInCommentNotification::class);
});

test('replying to your OWN comment does not insert a self-mention or self-notify', function () {
    Notification::fake();

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Replying to myself',
        'parent_comment_id' => $this->topLevel->id,
    ]);

    $response->assertCreated();

    $reply = Comment::where('body', 'like', '%Replying to myself%')->firstOrFail();
    expect($reply->body)->not->toContain('@'.$this->management->name);
    expect($reply->mentionedUsers()->count())->toBe(0);
    Notification::assertNothingSentTo($this->management);
});

test('a reply containing both the auto-mention and a manually-typed mention of someone else notifies both people independently', function () {
    Notification::fake();
    $mentioned = makeEligibleStaff($this->org, $this->dept);
    $replier = makeEligibleStaff($this->org, $this->dept);

    $this->actingAs($replier)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Also cc @'.$mentioned->name,
        'parent_comment_id' => $this->topLevel->id,
        'mentioned_user_ids' => [$mentioned->id],
    ])->assertCreated();

    // Two independent reasons, two independent recipients, same
    // notification type for both (the existing mention system) — the
    // auto-mention of the comment's author (management) and the
    // manually-typed mention of $mentioned both fire.
    Notification::assertSentTo($this->management, MentionedInCommentNotification::class);
    Notification::assertSentTo($mentioned, MentionedInCommentNotification::class);

    $reply = Comment::where('body', 'like', '%Also cc%')->firstOrFail();
    expect($reply->mentionedUsers()->pluck('users.id')->sort()->values()->all())
        ->toBe(collect([$this->management->id, $mentioned->id])->sort()->values()->all());
});

test('the comment list renders top-level comments with a single flat, chronologically-ordered list of replies underneath, no deeper nesting', function () {
    $staff = makeEligibleStaff($this->org, $this->dept);
    $reply1 = Comment::create(['task_id' => $this->task->id, 'user_id' => $staff->id, 'parent_comment_id' => $this->topLevel->id, 'body' => 'Reply 1']);
    // A reply to reply1 (per the flat model, still parented to the same
    // top-level comment) — this must NOT render at any deeper visual
    // level than reply1 itself.
    $reply2 = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'parent_comment_id' => $this->topLevel->id, 'body' => 'Reply 2, addressing reply 1']);

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments");
    $response->assertOk();

    $comments = collect($response->json('comments'));
    expect($comments)->toHaveCount(3);

    $repliesToTopLevel = $comments->where('parent_comment_id', $this->topLevel->id)->sortBy('id')->values();
    expect($repliesToTopLevel->pluck('id')->all())->toBe([$reply1->id, $reply2->id]);

    // Rendered page: both replies are direct children of the SAME
    // .replies-list (one flat level), not nested inside one another.
    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();
    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();
    $xpath = new DOMXPath($document);

    $repliesList = null;
    foreach ($xpath->query('//*[contains(@class, "replies-list")]') as $node) {
        if ($xpath->query('.//*[@data-comment-id="'.$reply1->id.'"]', $node)->length > 0) {
            $repliesList = $node;
        }
    }
    expect($repliesList)->not->toBeNull();

    // Both reply ids are DIRECT children of this one replies-list, and
    // reply2's card is not nested inside reply1's card.
    $directReplyCards = $xpath->query('./*[contains(@class, "comment-card")]', $repliesList);
    $directIds = [];
    foreach ($directReplyCards as $card) {
        $directIds[] = $card->getAttribute('data-comment-id');
    }
    expect($directIds)->toEqualCanonicalizing([(string) $reply1->id, (string) $reply2->id]);

    $nestedInsideReply1 = $xpath->query('.//*[@data-comment-id="'.$reply1->id.'"]//*[@data-comment-id="'.$reply2->id.'"]');
    expect($nestedInsideReply1->length)->toBe(0);
});

test('every comment, including a reply, shows a Reply action', function () {
    $reply = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'parent_comment_id' => $this->topLevel->id, 'body' => 'A reply']);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();
    $xpath = new DOMXPath($document);

    foreach ([$this->topLevel->id, $reply->id] as $id) {
        $card = null;
        foreach ($xpath->query('//*[contains(@class, "comment-card")]') as $node) {
            if ($node->getAttribute('data-comment-id') === (string) $id) {
                $card = $node;
            }
        }
        expect($card)->not->toBeNull();
        expect($xpath->query('.//button[contains(@class, "reply-comment-btn")]', $card)->length)->toBe(1);
    }
});

test('a new reply arriving via the polling endpoint is positioned correctly among a thread\'s existing replies', function () {
    $staff = makeEligibleStaff($this->org, $this->dept);
    $older = Comment::create(['task_id' => $this->task->id, 'user_id' => $staff->id, 'parent_comment_id' => $this->topLevel->id, 'body' => 'Older reply']);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Newer reply',
        'parent_comment_id' => $this->topLevel->id,
    ])->assertCreated();
    $newer = Comment::where('body', 'like', '%Newer reply%')->firstOrFail();

    // The polling endpoint returns every comment in created_at ascending
    // order regardless of who posted what — the client's insertInOrder()
    // relies on this fetch order (and the auto-increment id it carries)
    // to place a newly-discovered reply at its correct position rather
    // than always at the end.
    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments");
    $response->assertOk();

    $ids = collect($response->json('comments'))->pluck('id')->values()->all();
    $olderPos = array_search($older->id, $ids);
    $newerPos = array_search($newer->id, $ids);
    expect($olderPos)->not->toBeFalse();
    expect($newerPos)->toBeGreaterThan($olderPos);
});
