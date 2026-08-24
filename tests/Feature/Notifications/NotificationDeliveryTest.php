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
