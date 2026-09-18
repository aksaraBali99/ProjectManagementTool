<?php

use App\Models\Department;
use App\Models\NotificationSetting;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Notifications\AuditEventMailNotification;
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

    $this->recipient = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->recipient->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    $this->task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Delivery test task',
        'description' => 'Same description',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function changeTaskStatus($test)
{
    return $test->actingAs($test->management)->put("/tasks/{$test->task->id}", [
        'project_id' => $test->projectA->id,
        'department_id' => $test->deptA->id,
        'title' => 'Delivery test task',
        'description' => 'Same description',
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);
}

function assignExistingTask($test, User $actor, ?int $assigneeId)
{
    return $test->actingAs($actor)->put("/tasks/{$test->task->id}", [
        'project_id' => $test->projectA->id,
        'department_id' => $test->deptA->id,
        'assignee_id' => $assigneeId,
        'title' => 'Delivery test task',
        'description' => 'Same description',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
}

function createTaskWithAssignee($test, User $actor, int $assigneeId)
{
    return $test->actingAs($actor)->post('/tasks', [
        'project_id' => $test->projectA->id,
        'department_id' => $test->deptA->id,
        'assignee_id' => $assigneeId,
        'title' => 'Brand new task',
        'description' => 'A fresh task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
}

function givePersonalTaskAssignedRule($user, bool $isActive = true)
{
    return NotificationSetting::create([
        'owner_id' => $user->id,
        'event_type' => 'task_assigned',
        'channel' => 'in_app',
        'recipients' => null,
        'is_active' => $isActive,
    ]);
}

test('triggering a configured event creates an in-app notification for the configured recipient', function () {
    NotificationSetting::create([
        'owner_id' => $this->recipient->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$this->recipient->id]],
        'is_active' => true,
    ]);

    changeTaskStatus($this);

    expect($this->recipient->fresh()->notifications()->count())->toBe(1);

    $notification = $this->recipient->fresh()->notifications()->first();
    expect($notification->data['entity_type'])->toBe('task')
        ->and($notification->data['entity_id'])->toBe($this->task->id)
        ->and($notification->data['message'])->toContain('Delivery test task')
        ->and($notification->read_at)->toBeNull();
});

test('a user with a notification rule set to inactive does not receive that notification', function () {
    NotificationSetting::create([
        'owner_id' => $this->recipient->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$this->recipient->id]],
        'is_active' => false,
    ]);

    changeTaskStatus($this);

    expect($this->recipient->fresh()->notifications()->count())->toBe(0);
});

test('an email-channel rule queues an email notification for the configured recipient', function () {
    Notification::fake();

    NotificationSetting::create([
        'owner_id' => $this->recipient->id,
        'event_type' => 'task_status_changed',
        'channel' => 'email',
        'recipients' => ['type' => 'users', 'ids' => [$this->recipient->id]],
        'is_active' => true,
    ]);

    changeTaskStatus($this);

    Notification::assertSentTo($this->recipient, AuditEventMailNotification::class);
});

test('a personal "off" preference is respected even when an active admin broadcast rule would otherwise include the same user', function () {
    // The personal row (recipients null) is the user's own preference,
    // deliberately turned off.
    NotificationSetting::create([
        'owner_id' => $this->recipient->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => null,
        'is_active' => false,
    ]);

    // An admin-configured broadcast rule that would otherwise notify this
    // exact user for the same event_type/channel.
    NotificationSetting::create([
        'owner_id' => $this->management->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$this->recipient->id]],
        'is_active' => true,
    ]);

    changeTaskStatus($this);

    expect($this->recipient->fresh()->notifications()->count())->toBe(0);
});

test('a user with no personal rule for an event correctly receives notifications per the applicable admin rule', function () {
    NotificationSetting::create([
        'owner_id' => $this->management->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$this->recipient->id]],
        'is_active' => true,
    ]);

    changeTaskStatus($this);

    expect($this->recipient->fresh()->notifications()->count())->toBe(1);
});

test('assigning a subtask notifies the assignee via the same task_assigned event as task assignment', function () {
    $this->projectA->staff()->attach($this->recipient->id);

    NotificationSetting::create([
        'owner_id' => $this->recipient->id,
        'event_type' => 'task_assigned',
        'channel' => 'in_app',
        'recipients' => null,
        'is_active' => true,
    ]);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/subtasks", [
        'title' => 'Sub with an assignee',
        'assignee_id' => $this->recipient->id,
    ])->assertCreated();

    expect($this->recipient->fresh()->notifications()->count())->toBe(1);

    $notification = $this->recipient->fresh()->notifications()->first();
    expect($notification->data['entity_type'])->toBe('subtask')
        ->and($notification->data['message'])->toContain('Sub with an assignee');
});

test('reassigning a subtask notifies only the new assignee, not the previous one', function () {
    $this->projectA->staff()->attach($this->recipient->id);
    $previousAssignee = User::factory()->create();
    $this->projectA->staff()->attach($previousAssignee->id);
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $previousAssignee->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    foreach ([$this->recipient, $previousAssignee] as $user) {
        NotificationSetting::create([
            'owner_id' => $user->id,
            'event_type' => 'task_assigned',
            'channel' => 'in_app',
            'recipients' => null,
            'is_active' => true,
        ]);
    }

    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'assignee_id' => $previousAssignee->id]);
    $previousAssignee->notifications()->delete();

    $this->actingAs($this->management)->putJson("/subtasks/{$subtask->id}", [
        'assignee_id' => $this->recipient->id,
    ])->assertOk();

    expect($this->recipient->fresh()->notifications()->count())->toBe(1)
        ->and($previousAssignee->fresh()->notifications()->count())->toBe(0);
});

test('creating a subtask with no assignee sends no task_assigned notification', function () {
    NotificationSetting::create([
        'owner_id' => $this->recipient->id,
        'event_type' => 'task_assigned',
        'channel' => 'in_app',
        'recipients' => null,
        'is_active' => true,
    ]);

    $this->actingAs($this->management)->postJson("/tasks/{$this->task->id}/subtasks", [
        'title' => 'Unassigned sub',
    ])->assertCreated();

    expect($this->recipient->fresh()->notifications()->count())->toBe(0);
});

test('a user matching two different admin rules for the same event_type receives exactly one notification, not two', function () {
    NotificationSetting::create([
        'owner_id' => $this->management->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$this->recipient->id]],
        'is_active' => true,
    ]);

    NotificationSetting::create([
        'owner_id' => $this->management->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'role', 'role' => 'staff'],
        'is_active' => true,
    ]);

    changeTaskStatus($this);

    expect($this->recipient->fresh()->notifications()->count())->toBe(1);
});

test('assigning a task to another user creates a notification for that user', function () {
    $this->projectA->staff()->attach($this->recipient->id);
    givePersonalTaskAssignedRule($this->recipient);

    assignExistingTask($this, $this->management, $this->recipient->id)->assertRedirect();

    expect($this->recipient->fresh()->notifications()->count())->toBe(1);

    $notification = $this->recipient->fresh()->notifications()->first();
    expect($notification->data['entity_type'])->toBe('task')
        ->and($notification->data['entity_id'])->toBe($this->task->id);
});

test('reassigning a task from one user to another notifies the new assignee only, not the old one', function () {
    $previousAssignee = User::factory()->create();
    $this->projectA->staff()->attach([$this->recipient->id, $previousAssignee->id]);
    givePersonalTaskAssignedRule($this->recipient);
    givePersonalTaskAssignedRule($previousAssignee);

    assignExistingTask($this, $this->management, $previousAssignee->id)->assertRedirect();
    $previousAssignee->notifications()->delete();

    assignExistingTask($this, $this->management, $this->recipient->id)->assertRedirect();

    expect($this->recipient->fresh()->notifications()->count())->toBe(1)
        ->and($previousAssignee->fresh()->notifications()->count())->toBe(0);
});

test('a user assigning a task to themselves does not trigger any notification', function () {
    $this->projectA->staff()->attach($this->management->id);
    givePersonalTaskAssignedRule($this->management);

    assignExistingTask($this, $this->management, $this->management->id)->assertRedirect();

    expect($this->management->fresh()->notifications()->count())->toBe(0);
});

test('a manager reassigning a task away from themselves to another user still notifies that assignee', function () {
    $this->projectA->staff()->attach([$this->management->id, $this->recipient->id]);
    givePersonalTaskAssignedRule($this->recipient);

    // The task starts assigned to the manager themselves (self-assignment,
    // so no notification from this step) before they hand it off — this
    // confirms the exclusion check compares the actor against the NEW
    // assignee only, not against who held the task before.
    assignExistingTask($this, $this->management, $this->management->id)->assertRedirect();

    assignExistingTask($this, $this->management, $this->recipient->id)->assertRedirect();

    expect($this->recipient->fresh()->notifications()->count())->toBe(1);
});

test("the assignee's personal preference to opt out of task_assigned notifications is respected", function () {
    $this->projectA->staff()->attach($this->recipient->id);
    givePersonalTaskAssignedRule($this->recipient, isActive: false);

    assignExistingTask($this, $this->management, $this->recipient->id)->assertRedirect();

    expect($this->recipient->fresh()->notifications()->count())->toBe(0);
});

test('creating a brand new task with an assignee triggers a task_assigned notification', function () {
    $this->projectA->staff()->attach($this->recipient->id);
    givePersonalTaskAssignedRule($this->recipient);

    createTaskWithAssignee($this, $this->management, $this->recipient->id)->assertRedirect();

    expect($this->recipient->fresh()->notifications()->count())->toBe(1);

    $notification = $this->recipient->fresh()->notifications()->first();
    expect($notification->data['entity_type'])->toBe('task')
        ->and($notification->data['message'])->toContain('Brand new task');
});

test('a team "everyone" task_assigned rule notifies only the actual new assignee, not other staff with no personal preference', function () {
    $bystander = User::factory()->create();
    $this->projectA->staff()->attach([$this->recipient->id, $bystander->id]);
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $bystander->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    // Team-level default, not a personal opt-in from either user — this is
    // the "turn it on for everyone" rule from the Team Notification Rules
    // section, with no recipient list of its own.
    NotificationSetting::create([
        'owner_id' => $this->management->id,
        'event_type' => 'task_assigned',
        'channel' => 'in_app',
        'recipients' => ['type' => 'all'],
        'is_active' => true,
    ]);

    assignExistingTask($this, $this->management, $this->recipient->id)->assertRedirect();

    expect($this->recipient->fresh()->notifications()->count())->toBe(1)
        ->and($bystander->fresh()->notifications()->count())->toBe(0);
});

test('a team role-based task_assigned rule notifies only the actual new assignee within that role, not the whole role', function () {
    $bystander = User::factory()->create();
    $this->projectA->staff()->attach([$this->recipient->id, $bystander->id]);
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $bystander->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    // Before the assignee gate was added to resolveAdminChannels(), this
    // exact rule shape would have notified every staff member on every
    // assignment, not just the one being assigned.
    NotificationSetting::create([
        'owner_id' => $this->management->id,
        'event_type' => 'task_assigned',
        'channel' => 'in_app',
        'recipients' => ['type' => 'role', 'role' => 'staff'],
        'is_active' => true,
    ]);

    assignExistingTask($this, $this->management, $this->recipient->id)->assertRedirect();

    expect($this->recipient->fresh()->notifications()->count())->toBe(1)
        ->and($bystander->fresh()->notifications()->count())->toBe(0);
});

test('self-assignment still sends nothing even with an active team "everyone" task_assigned rule in place', function () {
    $this->projectA->staff()->attach($this->management->id);

    NotificationSetting::create([
        'owner_id' => $this->management->id,
        'event_type' => 'task_assigned',
        'channel' => 'in_app',
        'recipients' => ['type' => 'all'],
        'is_active' => true,
    ]);

    assignExistingTask($this, $this->management, $this->management->id)->assertRedirect();

    expect($this->management->fresh()->notifications()->count())->toBe(0);
});
