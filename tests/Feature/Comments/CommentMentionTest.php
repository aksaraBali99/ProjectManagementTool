<?php

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

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->first()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Ship the launch page',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function makeProjectMember(Project $project): User
{
    $user = User::factory()->create();
    $project->staff()->attach($user->id);

    return $user;
}

test('mentioning a project member in a comment notifies them once, even if mentioned twice', function () {
    Notification::fake();
    $mentioned = makeProjectMember($this->projectA);

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '@'.$mentioned->name.' please check this, @'.$mentioned->name.' thanks',
        'mentioned_user_ids' => [$mentioned->id, $mentioned->id],
    ]);

    $response->assertCreated();
    $comment = Comment::firstOrFail();
    expect($comment->mentionedUsers()->pluck('users.id')->all())->toBe([$mentioned->id]);

    Notification::assertSentToTimes($mentioned, MentionedInCommentNotification::class, 1);
});

test('the mention notification links to the task edit page and names the task', function () {
    Notification::fake();
    $mentioned = makeProjectMember($this->projectA);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '@'.$mentioned->name,
        'mentioned_user_ids' => [$mentioned->id],
    ])->assertCreated();

    Notification::assertSentTo($mentioned, function (MentionedInCommentNotification $notification) {
        $data = $notification->toDatabase($notification);

        return $data['message'] === 'You are mentioned in task "Ship the launch page".'
            && $data['link'] === route('tasks.edit', $this->task->id);
    });
});

test('mentioning a user not eligible for the task is silently dropped, not notified', function () {
    Notification::fake();
    $outsider = User::factory()->create();

    $response = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'cc @'.$outsider->name,
        'mentioned_user_ids' => [$outsider->id],
    ]);

    $response->assertCreated();
    $comment = Comment::firstOrFail();
    expect($comment->mentionedUsers()->count())->toBe(0);

    Notification::assertNotSentTo($outsider, MentionedInCommentNotification::class);
});

test('mentioning yourself does not send yourself a notification', function () {
    Notification::fake();

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => 'note to self @'.$this->management->name,
        'mentioned_user_ids' => [$this->management->id],
    ])->assertCreated();

    Notification::assertNotSentTo($this->management, MentionedInCommentNotification::class);
});

test('editing a comment to add a new mention notifies only the newly added user', function () {
    Notification::fake();
    $first = makeProjectMember($this->projectA);
    $second = makeProjectMember($this->projectA);

    $created = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '@'.$first->name,
        'mentioned_user_ids' => [$first->id],
    ])->json('comment');

    Notification::assertSentToTimes($first, MentionedInCommentNotification::class, 1);

    $this->actingAs($this->management)->putJson("/comments/{$created['id']}", [
        'body' => '@'.$first->name.' and @'.$second->name,
        'mentioned_user_ids' => [$first->id, $second->id],
    ])->assertOk();

    // First was already mentioned before this edit — no repeat notification.
    Notification::assertSentToTimes($first, MentionedInCommentNotification::class, 1);
    Notification::assertSentToTimes($second, MentionedInCommentNotification::class, 1);
});

test('removing a mention on edit drops the pivot row without notifying anyone new', function () {
    Notification::fake();
    $mentioned = makeProjectMember($this->projectA);

    $created = $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/comments", [
        'body' => '@'.$mentioned->name,
        'mentioned_user_ids' => [$mentioned->id],
    ])->json('comment');

    $this->actingAs($this->management)->putJson("/comments/{$created['id']}", [
        'body' => 'never mind',
        'mentioned_user_ids' => [],
    ])->assertOk();

    expect(Comment::find($created['id'])->mentionedUsers()->count())->toBe(0);
    Notification::assertSentToTimes($mentioned, MentionedInCommentNotification::class, 1);
});
