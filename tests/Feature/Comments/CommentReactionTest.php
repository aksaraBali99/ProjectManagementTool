<?php

use App\Models\AccessPermission;
use App\Models\Comment;
use App\Models\CommentReaction;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

/**
 * Emoji reactions on comments and replies (task #70, phase 4 — final).
 * Permission is the same rule as commenting itself (anyone who can view the
 * task), enforced server-side in CommentReactionController, not just by
 * hiding the picker client-side. Deliberately NO notification of any kind —
 * see CommentReactionController's own docblock for why.
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

    $this->comment = Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $this->management->id,
        'body' => 'Original comment',
    ]);
});

function makeReactionEligibleStaff(Organization $org, Department $department): User
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

function makeReactionClient(Organization $org, Project $project): User
{
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->first()->id,
    ]);
    $project->clients()->attach($client->id);

    return $client;
}

test('reacting to a comment for the first time creates a comment_reactions row', function () {
    $response = $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", [
        'emoji' => '👍',
    ]);

    $response->assertOk();
    expect(CommentReaction::count())->toBe(1);
    $reaction = CommentReaction::firstOrFail();
    expect($reaction->comment_id)->toBe($this->comment->id);
    expect($reaction->user_id)->toBe($this->management->id);
    expect($reaction->emoji)->toBe('👍');
});

test('reacting again with a different emoji replaces the existing reaction — still exactly one row for that user and comment', function () {
    $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();
    $firstId = CommentReaction::firstOrFail()->id;

    $response = $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '🎉']);

    $response->assertOk();
    expect(CommentReaction::count())->toBe(1);
    $reaction = CommentReaction::firstOrFail();
    expect($reaction->id)->toBe($firstId); // same row, updated in place — not a second insert
    expect($reaction->emoji)->toBe('🎉');
});

test('reacting again with the same emoji removes the reaction — toggle off', function () {
    $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();
    expect(CommentReaction::count())->toBe(1);

    $response = $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍']);

    $response->assertOk();
    expect(CommentReaction::count())->toBe(0);
    expect($response->json('reactions'))->toBe([]);
});

test('reactions work identically on a reply, not just a top-level comment', function () {
    $reply = Comment::create([
        'task_id' => $this->task->id,
        'user_id' => $this->management->id,
        'parent_comment_id' => $this->comment->id,
        'body' => 'A reply',
    ]);
    $staff = makeReactionEligibleStaff($this->org, $this->dept);

    $this->actingAs($staff)->postJson("/comments/{$reply->id}/reactions", ['emoji' => '❤️'])->assertOk();
    expect(CommentReaction::where('comment_id', $reply->id)->count())->toBe(1);

    // Same replace/toggle rules apply — a different emoji replaces, not adds.
    $this->actingAs($staff)->postJson("/comments/{$reply->id}/reactions", ['emoji' => '🔥'])->assertOk();
    expect(CommentReaction::where('comment_id', $reply->id)->count())->toBe(1);
    expect(CommentReaction::where('comment_id', $reply->id)->first()->emoji)->toBe('🔥');
});

test('multiple different people can each react to the same comment, with their own independent reaction', function () {
    $staff = makeReactionEligibleStaff($this->org, $this->dept);
    $client = makeReactionClient($this->org, $this->project);

    $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();
    $this->actingAs($staff)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();
    $response = $this->actingAs($client)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '🎉']);

    $response->assertOk();
    expect(CommentReaction::count())->toBe(3);

    $reactions = collect($response->json('reactions'))->keyBy('emoji');
    expect($reactions['👍']['count'])->toBe(2);
    expect($reactions['👍']['user_names'])->toEqualCanonicalizing([$this->management->name, $staff->name]);
    expect($reactions['🎉']['count'])->toBe(1);
    expect($reactions['🎉']['user_names'])->toBe([$client->name]);
});

test('reacted_by_me is true only for the viewer\'s own reaction, and differs correctly per viewer', function () {
    $staff = makeReactionEligibleStaff($this->org, $this->dept);
    $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();

    $asManagement = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->assertOk();
    $asStaff = $this->actingAs($staff)->getJson("/tasks/{$this->task->id}/comments")->assertOk();

    $managementView = collect($asManagement->json('comments'))->firstWhere('id', $this->comment->id)['reactions'][0];
    $staffView = collect($asStaff->json('comments'))->firstWhere('id', $this->comment->id)['reactions'][0];

    expect($managementView['reacted_by_me'])->toBeTrue();
    expect($staffView['reacted_by_me'])->toBeFalse();
});

test('reaction counts and who-reacted are aggregated in a single query, regardless of how many comments/reactions the page has', function () {
    // Baseline: this->comment plus one reaction.
    $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();

    // A throwaway warm-up request first — the very first call to this
    // endpoint in the test pays for some request-scoped caches
    // (User::hasPermission()/isSuperAdmin()/isOwner()'s own memoization,
    // see their docblocks) that later calls on the SAME user don't. Without
    // this, comparing "first call" against "later call" would show a drop
    // in query count that's just cache-warming, not evidence either way
    // about whether reactions/comments scale — both the baseline and the
    // scaled-up measurement below are taken after that one-time cost is
    // already paid, so the comparison isolates what actually varies with
    // data volume.
    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->assertOk();

    DB::enableQueryLog();
    $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->assertOk();
    $baselineQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    // Scale up: several more comments, each with several reactions from
    // different people.
    $reactors = User::factory()->count(3)->create()->each(function (User $user) {
        OrgMember::create([
            'organization_id' => $this->org->id,
            'user_id' => $user->id,
            'role_id' => Role::where('slug', 'staff')->first()->id,
        ]);
        AccessPermission::create([
            'user_id' => $user->id,
            'organization_id' => $this->org->id,
            'department_id' => $this->dept->id,
            'allowed' => true,
        ]);
    });

    collect(range(1, 5))->each(function (int $i) use ($reactors) {
        $comment = Comment::create([
            'task_id' => $this->task->id,
            'user_id' => $this->management->id,
            'body' => 'Comment '.$i,
        ]);
        foreach ($reactors as $reactor) {
            CommentReaction::create(['comment_id' => $comment->id, 'user_id' => $reactor->id, 'emoji' => '👍']);
        }
    });

    // Setting up the scaled-up fixture above fires its own queries
    // (CommentObserver's audit-log/notification-settings checks on each
    // Comment::create) — flush those out so what's actually being compared
    // is the /tasks/{id}/comments request itself, not fixture setup noise.
    DB::flushQueryLog();

    DB::enableQueryLog();
    $response = $this->actingAs($this->management)->getJson("/tasks/{$this->task->id}/comments")->assertOk();
    $scaledUpQueries = count(DB::getQueryLog());
    DB::flushQueryLog();

    expect(collect($response->json('comments')))->toHaveCount(6);
    expect($scaledUpQueries)->toBe($baselineQueries);
});

test('a staff member with department access can react; permission is enforced server-side, not just by hiding the picker', function () {
    $staff = makeReactionEligibleStaff($this->org, $this->dept);

    $this->actingAs($staff)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();
});

test('a staff member WITHOUT department access is forbidden from reacting, even by posting directly to the endpoint', function () {
    $otherDept = Department::create(['organization_id' => $this->org->id, 'name' => 'Sales', 'color' => '#111111']);
    $staffWithoutAccess = makeReactionEligibleStaff($this->org, $otherDept);

    $this->actingAs($staffWithoutAccess)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])
        ->assertForbidden();

    expect(CommentReaction::count())->toBe(0);
});

test('management can react', function () {
    $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();
});

test('the project\'s client can react', function () {
    $client = makeReactionClient($this->org, $this->project);

    $this->actingAs($client)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();
});

test('reacting to a comment sends no notification of any kind', function () {
    Notification::fake();
    $staff = makeReactionEligibleStaff($this->org, $this->dept);

    $this->actingAs($staff)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '👍'])->assertOk();

    Notification::assertNothingSent();
});

test('a reaction added by one user appears via the polling endpoint for a second user\'s session, without a page reload', function () {
    $staff = makeReactionEligibleStaff($this->org, $this->dept);

    // Staff's own session sees no reaction on this comment yet.
    $before = $this->actingAs($staff)->getJson("/tasks/{$this->task->id}/comments")->assertOk();
    expect(collect($before->json('comments'))->firstWhere('id', $this->comment->id)['reactions'])->toBe([]);

    // Management reacts, in a completely separate request/session.
    $this->actingAs($this->management)->postJson("/comments/{$this->comment->id}/reactions", ['emoji' => '🎉'])->assertOk();

    // Staff's next poll tick picks it up.
    $after = $this->actingAs($staff)->getJson("/tasks/{$this->task->id}/comments")->assertOk();
    $comment = collect($after->json('comments'))->firstWhere('id', $this->comment->id);
    expect($comment['reactions'])->toBe([[
        'emoji' => '🎉',
        'count' => 1,
        'user_names' => [$this->management->name],
        'reacted_by_me' => false,
    ]]);
    // The hash the client's syncComments() diffs on must have actually
    // changed from the "no reactions" state, or the poll would never
    // trigger a re-render.
    expect($comment['reactions_hash'])->not->toBe(md5(json_encode([])));
});
