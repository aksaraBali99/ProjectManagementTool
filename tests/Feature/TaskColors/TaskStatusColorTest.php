<?php

use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskStatusColor;
use App\Models\User;

beforeEach(function () {
    $this->owner = createOwner();

    $this->management = User::factory()->create();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->first()->id]);

    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Status color test task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

/**
 * @param  array<string, array{background_color?: string, text_color?: string}>  $overrides
 * @return array<string, array{background_color: string, text_color: string}>
 */
function allStatusColorsPayload(array $overrides = []): array
{
    $defaults = [
        'pending' => ['background_color' => '#F1EFE8', 'text_color' => '#5F5E5A'],
        'in_progress' => ['background_color' => '#E1F5EE', 'text_color' => '#0F6E56'],
        'in_review' => ['background_color' => '#FAEEDA', 'text_color' => '#854F0B'],
        'completed' => ['background_color' => '#EAF3DE', 'text_color' => '#3B6D11'],
    ];

    return array_replace_recursive($defaults, $overrides);
}

test('a non-owner/non-super-admin cannot access the status colors settings page', function () {
    $this->actingAs($this->management)->get('/task-colors')->assertForbidden();
    $this->actingAs($this->management)->put('/task-colors/status', [
        'colors' => allStatusColorsPayload(),
    ])->assertForbidden();
});

test('an owner can view the status colors settings page with current colors pre-filled', function () {
    $response = $this->actingAs($this->owner)->get('/task-colors');

    $response->assertOk();
    $response->assertSee('Status & Priority Colors');
    $response->assertSee('value="#F1EFE8"', false);
});

test('changing the pending status color updates Kanban, Dashboard, Task list, and Analytics', function () {
    $newBackground = '#123456';
    $newText = '#ABCDEF';

    $update = $this->actingAs($this->owner)->put('/task-colors/status', [
        'colors' => allStatusColorsPayload(['pending' => ['background_color' => $newBackground, 'text_color' => $newText]]),
    ]);
    $update->assertRedirect(route('task-colors.edit'));

    $kanban = $this->actingAs($this->owner)->get("/kanban/{$this->orgA->id}");
    $kanban->assertOk()->assertSee($newBackground, false);

    $dashboard = $this->actingAs($this->owner)->get("/dashboard/{$this->orgA->id}");
    $dashboard->assertOk()->assertSee($newText, false);

    $taskList = $this->actingAs($this->owner)->get("/tasks/{$this->orgA->id}");
    $taskList->assertOk()->assertSee($newBackground, false);

    $analytics = $this->actingAs($this->owner)->get('/analytics');
    $analytics->assertOk()->assertSee($newBackground, false);
});

test('changing a status color takes effect immediately without any manual cache clear', function () {
    // Warm the cache with the original colors first, same as any normal
    // page view would before an admin ever visits the settings page.
    expect(TaskStatus::Pending->badgeBackground())->toBe('#F1EFE8');

    $this->actingAs($this->owner)->put('/task-colors/status', [
        'colors' => allStatusColorsPayload(['pending' => ['background_color' => '#654321', 'text_color' => '#111111']]),
    ]);

    expect(TaskStatus::Pending->badgeBackground())->toBe('#654321')
        ->and(TaskStatus::Pending->badgeText())->toBe('#111111');
});

test('saving updates the task_status_colors row for that status only', function () {
    $this->actingAs($this->owner)->put('/task-colors/status', [
        'colors' => allStatusColorsPayload(['completed' => ['background_color' => '#00FF00', 'text_color' => '#003300']]),
    ]);

    $this->assertDatabaseHas('task_status_colors', [
        'status' => 'completed',
        'background_color' => '#00FF00',
        'text_color' => '#003300',
    ]);
    $this->assertDatabaseHas('task_status_colors', [
        'status' => 'pending',
        'background_color' => '#F1EFE8',
    ]);
});

test('submitting an invalid hex color is rejected', function () {
    $response = $this->actingAs($this->owner)->put('/task-colors/status', [
        'colors' => allStatusColorsPayload(['pending' => ['background_color' => 'not-a-color', 'text_color' => '#5F5E5A']]),
    ]);

    $response->assertSessionHasErrors();
    expect(TaskStatusColor::where('status', 'pending')->first()->background_color)->toBe('#F1EFE8');
});

test('submitting fewer than all four statuses is rejected', function () {
    $payload = allStatusColorsPayload();
    unset($payload['completed']);

    $response = $this->actingAs($this->owner)->put('/task-colors/status', ['colors' => $payload]);

    $response->assertSessionHasErrors('colors');
});
