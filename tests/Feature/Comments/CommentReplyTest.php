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
use App\Notifications\RepliedToCommentNotification;
use Illuminate\Support\Facades\Notification;

/**
 * One level of threaded replies on Task comments (task #70, phase 2):
 * a top-level comment plus a flat list of replies underneath it — never
 * a reply-to-a-reply. Replying follows the exact same permission rule as
 * posting a top-level comment (CommentPolicy::create() plus viewing the
 * task) and reuses the @mention notification's "always fires, no
 * opt-out" pattern via a distinct RepliedToCommentNotification.
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

test('a reply can be created under a top-level comment and is correctly associated via parent_comment_id', function () {
    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'A reply',
        'parent_comment_id' => $this->topLevel->id,
    ]);

    $response->assertCreated()->assertJsonPath('comment.parent_comment_id', $this->topLevel->id);

    $reply = Comment::where('body', 'A reply')->firstOrFail();
    expect($reply->parent_comment_id)->toBe($this->topLevel->id);
    expect($this->topLevel->fresh()->replies()->pluck('id')->all())->toBe([$reply->id]);
});

test('a reply whose parent is itself a reply is rejected — threading is capped at one level', function () {
    $firstReply = Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $this->management->id,
        'parent_comment_id' => $this->topLevel->id,
        'body' => 'First reply',
    ]);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'A reply to a reply',
        'parent_comment_id' => $firstReply->id,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('parent_comment_id');
    expect(Comment::where('body', 'A reply to a reply')->exists())->toBeFalse();
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

test('a staff member without department access to the task cannot reply, even with a valid parent_comment_id', function () {
    // A real org member (so the multi-tenancy global scope doesn't hide
    // the task from them with a 404 before authorization even runs), but
    // with no access_permissions grant to this task's department — the
    // same TaskPolicy::view() rule a top-level comment is already gated
    // by.
    $otherDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Sales', 'color' => '#000000']);
    $staffWithoutAccess = makeEligibleStaff($this->org, $otherDept);

    $this->actingAs($staffWithoutAccess)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Should not work',
        'parent_comment_id' => $this->topLevel->id,
    ])->assertForbidden();

    expect($this->topLevel->fresh()->replies()->count())->toBe(0);
});

test('replying notifies the original comment\'s author, unconditionally', function () {
    Notification::fake();
    $staff = makeEligibleStaff($this->org, $this->dept);

    $this->actingAs($staff)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'A reply',
        'parent_comment_id' => $this->topLevel->id,
    ])->assertCreated();

    Notification::assertSentTo($this->management, RepliedToCommentNotification::class);
});

test('a user replying to their own comment does not trigger a notification', function () {
    Notification::fake();

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Replying to myself',
        'parent_comment_id' => $this->topLevel->id,
    ])->assertCreated();

    Notification::assertNothingSentTo($this->management);
});

test('a reply that both replies to someone and mentions someone else fires both notifications independently', function () {
    Notification::fake();
    $mentioned = makeEligibleStaff($this->org, $this->dept);
    $replier = makeEligibleStaff($this->org, $this->dept);

    $this->actingAs($replier)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Reply that mentions @'.$mentioned->name,
        'parent_comment_id' => $this->topLevel->id,
        'mentioned_user_ids' => [$mentioned->id],
    ])->assertCreated();

    // Two different recipients, two different notification types, for
    // the same single reply.
    Notification::assertSentTo($this->management, RepliedToCommentNotification::class);
    Notification::assertSentTo($mentioned, MentionedInCommentNotification::class);
    Notification::assertNotSentTo($mentioned, RepliedToCommentNotification::class);
    Notification::assertNotSentTo($this->management, MentionedInCommentNotification::class);
});

test('a reply that mentions the SAME person as the original comment\'s author fires both notifications to them', function () {
    Notification::fake();
    $replier = makeEligibleStaff($this->org, $this->dept);

    // $this->management authored the top-level comment AND gets @mentioned
    // in the reply — two independent reasons to notify the same person.
    $this->actingAs($replier)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'Reply mentioning @'.$this->management->name,
        'parent_comment_id' => $this->topLevel->id,
        'mentioned_user_ids' => [$this->management->id],
    ])->assertCreated();

    Notification::assertSentTo($this->management, RepliedToCommentNotification::class);
    Notification::assertSentTo($this->management, MentionedInCommentNotification::class);
});

test('the comment list groups top-level comments with their replies nested underneath, from a single fetch', function () {
    $reply1 = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'parent_comment_id' => $this->topLevel->id, 'body' => 'Reply 1']);
    $reply2 = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'parent_comment_id' => $this->topLevel->id, 'body' => 'Reply 2']);
    $secondTopLevel = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'body' => 'Second top-level comment']);

    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments");

    $response->assertOk();
    $comments = collect($response->json('comments'));

    // A single flat fetch (one query worth of data) — 4 rows total —
    // that the client groups by parent_comment_id, not a nested/grouped
    // response shape from the server.
    expect($comments)->toHaveCount(4);

    $topLevelIds = $comments->whereNull('parent_comment_id')->pluck('id')->all();
    expect($topLevelIds)->toEqualCanonicalizing([$this->topLevel->id, $secondTopLevel->id]);

    $repliesToFirst = $comments->where('parent_comment_id', $this->topLevel->id)->pluck('id')->all();
    expect($repliesToFirst)->toEqualCanonicalizing([$reply1->id, $reply2->id]);
});

test('the Edit Task page renders replies nested under their parent comment, with a Reply action on top-level comments only', function () {
    $reply = Comment::create(['task_id' => $this->task->id, 'user_id' => $this->management->id, 'parent_comment_id' => $this->topLevel->id, 'body' => 'A reply']);

    $page = $this->actingAs($this->management)->get("/tasks/{$this->task->id}/edit")->assertOk()->getContent();

    $document = new DOMDocument;
    libxml_use_internal_errors(true);
    $document->loadHTML('<?xml encoding="utf-8" ?>'.$page);
    libxml_clear_errors();
    $xpath = new DOMXPath($document);

    // The reply's .comment-card lives inside its parent thread's
    // .replies-list — the actual nesting the "underneath its parent"
    // requirement asks for, not just document order.
    $threadNode = null;
    foreach ($xpath->query('//*[contains(@class, "comment-thread")]') as $node) {
        if ($node->getAttribute('data-comment-id') === (string) $this->topLevel->id) {
            $threadNode = $node;
        }
    }
    expect($threadNode)->not->toBeNull();

    $nestedReply = $xpath->query('.//*[contains(@class, "replies-list")]//*[@data-comment-id="'.$reply->id.'"]', $threadNode);
    expect($nestedReply->length)->toBe(1);

    // Reply is a real <button> element (DOMDocument doesn't parse the
    // <script> block's own matching JS string literal as markup, so this
    // can't false-positive on that the way a plain substring search did).
    $replyButtons = $xpath->query('//button[contains(@class, "reply-comment-btn")]');
    expect($replyButtons->length)->toBe(1);
});
