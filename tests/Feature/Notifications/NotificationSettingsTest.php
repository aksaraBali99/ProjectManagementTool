<?php

use App\Models\NotificationSetting;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);

    $this->staff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->staff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);

    $this->otherStaff = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->orgA->id,
        'user_id' => $this->otherStaff->id,
        'role_id' => Role::where('slug', 'staff')->first()->id,
    ]);
});

test('any user can view and save their own notification preferences without manage_settings', function () {
    $this->actingAs($this->staff)->get('/notification-settings')->assertOk();

    $response = $this->actingAs($this->staff)->put('/notification-settings/mine', [
        'preferences' => [
            'task_assigned' => ['in_app' => '1'],
            'comment_added' => ['in_app' => '1', 'email' => '1'],
        ],
    ]);

    $response->assertRedirect('/notification-settings');

    $this->assertDatabaseHas('notification_settings', [
        'owner_id' => $this->staff->id,
        'event_type' => 'task_assigned',
        'channel' => 'in_app',
        'is_active' => true,
    ]);
    $this->assertDatabaseHas('notification_settings', [
        'owner_id' => $this->staff->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'is_active' => false,
    ]);
    $this->assertDatabaseHas('notification_settings', [
        'owner_id' => $this->staff->id,
        'event_type' => 'comment_added',
        'channel' => 'email',
        'is_active' => true,
    ]);
});

test('saving preferences twice updates the existing rows instead of creating duplicates', function () {
    $this->actingAs($this->staff)->put('/notification-settings/mine', [
        'preferences' => ['task_assigned' => ['in_app' => '1']],
    ]);
    $this->actingAs($this->staff)->put('/notification-settings/mine', [
        'preferences' => ['task_assigned' => ['in_app' => '1']],
    ]);

    expect(NotificationSetting::where('owner_id', $this->staff->id)
        ->where('event_type', 'task_assigned')
        ->where('channel', 'in_app')
        ->count())->toBe(1);
});

test('a staff user without manage_settings cannot create a rule targeting another specific user', function () {
    $response = $this->actingAs($this->staff)->post('/notification-settings/rules', [
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipient_type' => 'users',
        'user_ids' => [$this->otherStaff->id],
    ]);

    $response->assertForbidden();
    $this->assertDatabaseMissing('notification_settings', ['owner_id' => $this->staff->id, 'recipients' => json_encode(['type' => 'users', 'ids' => [$this->otherStaff->id]])]);
});

test('a staff user without manage_settings cannot create a role-based rule', function () {
    $this->actingAs($this->staff)->post('/notification-settings/rules', [
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipient_type' => 'role',
        'role' => 'management',
    ])->assertForbidden();
});

test('a staff user without manage_settings CAN create a rule that targets only themselves', function () {
    $response = $this->actingAs($this->staff)->post('/notification-settings/rules', [
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipient_type' => 'users',
        'user_ids' => [$this->staff->id],
    ]);

    $response->assertRedirect('/notification-settings');
    $this->assertDatabaseHas('notification_settings', ['owner_id' => $this->staff->id, 'event_type' => 'task_status_changed']);
});

test('an owner with manage_settings can create a rule targeting other users or a role', function () {
    $usersRule = $this->actingAs($this->owner)->post('/notification-settings/rules', [
        'event_type' => 'task_priority_changed',
        'channel' => 'in_app',
        'recipient_type' => 'users',
        'user_ids' => [$this->staff->id, $this->otherStaff->id],
    ]);
    $usersRule->assertRedirect('/notification-settings');

    $roleRule = $this->actingAs($this->owner)->post('/notification-settings/rules', [
        'event_type' => 'comment_added',
        'channel' => 'email',
        'recipient_type' => 'role',
        'role' => 'staff',
    ]);
    $roleRule->assertRedirect('/notification-settings');

    expect(NotificationSetting::where('owner_id', $this->owner->id)->whereNotNull('recipients')->count())->toBe(2);
});

test('a staff user cannot toggle or delete an admin-configured rule that isn\'t their own self-preference', function () {
    $rule = NotificationSetting::create([
        'owner_id' => $this->owner->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$this->otherStaff->id]],
        'is_active' => true,
    ]);

    $this->actingAs($this->staff)->patch("/notification-settings/rules/{$rule->id}/toggle")->assertForbidden();
    $this->actingAs($this->staff)->delete("/notification-settings/rules/{$rule->id}")->assertForbidden();
    expect($rule->fresh()->is_active)->toBeTrue();
});

test('an owner with manage_settings can toggle and delete any rule', function () {
    $rule = NotificationSetting::create([
        'owner_id' => $this->owner->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$this->staff->id]],
        'is_active' => true,
    ]);

    $this->actingAs($this->owner)->patch("/notification-settings/rules/{$rule->id}/toggle")->assertRedirect();
    expect($rule->fresh()->is_active)->toBeFalse();

    $this->actingAs($this->owner)->delete("/notification-settings/rules/{$rule->id}")->assertRedirect();
    $this->assertDatabaseMissing('notification_settings', ['id' => $rule->id]);
});

test('the Team Notification Rules section is hidden from a user without manage_settings', function () {
    $response = $this->actingAs($this->staff)->get('/notification-settings');

    $response->assertOk();
    $response->assertDontSee('Team Notification Rules');
});

test('the Team Notification Rules section is visible to an owner', function () {
    $response = $this->actingAs($this->owner)->get('/notification-settings');

    $response->assertOk();
    $response->assertSee('Team Notification Rules');
});

test('an owner cannot save a rule that exactly duplicates an existing one, even with the user_ids in a different order', function () {
    $this->actingAs($this->owner)->post('/notification-settings/rules', [
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipient_type' => 'users',
        'user_ids' => [$this->staff->id, $this->otherStaff->id],
    ])->assertRedirect('/notification-settings');

    $response = $this->actingAs($this->owner)->post('/notification-settings/rules', [
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipient_type' => 'users',
        'user_ids' => [$this->otherStaff->id, $this->staff->id],
    ]);

    $response->assertSessionHasErrors('duplicate');
    expect(NotificationSetting::where('event_type', 'task_status_changed')->where('channel', 'in_app')->count())->toBe(1);
});

test('an owner cannot save a duplicate role-based rule', function () {
    $this->actingAs($this->owner)->post('/notification-settings/rules', [
        'event_type' => 'comment_added',
        'channel' => 'email',
        'recipient_type' => 'role',
        'role' => 'staff',
    ])->assertRedirect('/notification-settings');

    $response = $this->actingAs($this->owner)->post('/notification-settings/rules', [
        'event_type' => 'comment_added',
        'channel' => 'email',
        'recipient_type' => 'role',
        'role' => 'staff',
    ]);

    $response->assertSessionHasErrors('duplicate');
    expect(NotificationSetting::where('event_type', 'comment_added')->where('channel', 'email')->count())->toBe(1);
});

test('the same recipients on a different channel is not considered a duplicate', function () {
    $this->actingAs($this->owner)->post('/notification-settings/rules', [
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipient_type' => 'users',
        'user_ids' => [$this->staff->id],
    ])->assertRedirect('/notification-settings');

    $response = $this->actingAs($this->owner)->post('/notification-settings/rules', [
        'event_type' => 'task_status_changed',
        'channel' => 'email',
        'recipient_type' => 'users',
        'user_ids' => [$this->staff->id],
    ]);

    $response->assertRedirect('/notification-settings');
    $response->assertSessionDoesntHaveErrors('duplicate');
    expect(NotificationSetting::where('event_type', 'task_status_changed')->count())->toBe(2);
});

test('a role-based rule displays the role\'s current editable name, not its slug', function () {
    Role::where('slug', 'staff')->update(['name' => 'Field Staff']);

    NotificationSetting::create([
        'owner_id' => $this->owner->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'role', 'role' => 'staff'],
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->owner)->get('/notification-settings');

    $response->assertOk();
    $response->assertSee('Role: Field Staff');
    $response->assertDontSee('Role: Staff');
});
