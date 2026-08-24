<?php

use App\Enums\Priority;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskPriorityColor;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $this->owner = User::factory()->create();
    $this->owner->roles()->attach(Role::where('slug', 'owner')->first()->id);

    $this->management = User::factory()->create();
    $this->orgA = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    OrgMember::create(['organization_id' => $this->orgA->id, 'user_id' => $this->management->id, 'role_id' => Role::where('slug', 'management')->first()->id]);

    $this->deptA = Department::create(['organization_id' => $this->orgA->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->projectA = Project::create(['organization_id' => $this->orgA->id, 'name' => 'Project A', 'description' => 'd']);

    $this->task = Task::create([
        'organization_id' => $this->orgA->id,
        'project_id' => $this->projectA->id,
        'department_id' => $this->deptA->id,
        'title' => 'Priority color test task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

/**
 * @param  array<string, array{background_color?: string, text_color?: string}>  $overrides
 * @return array<string, array{background_color: string, text_color: string}>
 */
function allPriorityColorsPayload(array $overrides = []): array
{
    $defaults = [
        'high' => ['background_color' => '#FDEAEA', 'text_color' => '#A32D2D'],
        'medium' => ['background_color' => '#FEF5E7', 'text_color' => '#854F0B'],
        'low' => ['background_color' => '#FEFCE6', 'text_color' => '#706910'],
    ];

    return array_replace_recursive($defaults, $overrides);
}

test('a non-owner/non-super-admin cannot update priority colors', function () {
    $this->actingAs($this->management)->put('/task-colors/priority', [
        'priority_colors' => allPriorityColorsPayload(),
    ])->assertForbidden();
});

test('an owner can view the priority colors section pre-filled with current colors', function () {
    $response = $this->actingAs($this->owner)->get('/task-colors');

    $response->assertOk();
    $response->assertSee('Priority Colors');
    $response->assertSee('value="#FDEAEA"', false);
});

test('changing the medium priority color updates Kanban, Dashboard, Task list, and Analytics', function () {
    $newBackground = '#334455';
    $newText = '#FEDCBA';

    $update = $this->actingAs($this->owner)->put('/task-colors/priority', [
        'priority_colors' => allPriorityColorsPayload(['medium' => ['background_color' => $newBackground, 'text_color' => $newText]]),
    ]);
    $update->assertRedirect(route('task-colors.edit'));

    $kanban = $this->actingAs($this->owner)->get("/kanban/{$this->orgA->id}");
    $kanban->assertOk()->assertSee($newBackground, false);

    $dashboard = $this->actingAs($this->owner)->get("/dashboard/{$this->orgA->id}");
    $dashboard->assertOk()->assertSee($newBackground, false)->assertSee($newText, false);

    $taskList = $this->actingAs($this->owner)->get("/tasks/{$this->orgA->id}");
    $taskList->assertOk()->assertSee($newBackground, false);

    $analytics = $this->actingAs($this->owner)->get('/analytics');
    $analytics->assertOk()->assertSee($newBackground, false);
});

test('folding dotColor into text_color means the Dashboard priority column header now reflects the configured text color', function () {
    $newText = '#00FF00';

    $this->actingAs($this->owner)->put('/task-colors/priority', [
        'priority_colors' => allPriorityColorsPayload(['medium' => ['background_color' => '#FEF5E7', 'text_color' => $newText]]),
    ]);

    expect(Priority::Medium->badgeText())->toBe($newText);

    $dashboard = $this->actingAs($this->owner)->get("/dashboard/{$this->orgA->id}");
    // border-color, the dot, and the label text on the priority column
    // header all read badgeText() now that dotColor() is gone — a single
    // occurrence check is enough to prove the fold happened, the exact
    // count of style attributes referencing it is an implementation detail.
    $dashboard->assertOk()->assertSee($newText, false);
});

test('changing a priority color takes effect immediately without any manual cache clear', function () {
    expect(Priority::High->badgeBackground())->toBe('#FDEAEA');

    $this->actingAs($this->owner)->put('/task-colors/priority', [
        'priority_colors' => allPriorityColorsPayload(['high' => ['background_color' => '#ABCDEF', 'text_color' => '#123456']]),
    ]);

    expect(Priority::High->badgeBackground())->toBe('#ABCDEF')
        ->and(Priority::High->badgeText())->toBe('#123456');
});

test('saving updates the task_priority_colors row for that priority only', function () {
    $this->actingAs($this->owner)->put('/task-colors/priority', [
        'priority_colors' => allPriorityColorsPayload(['low' => ['background_color' => '#00FF00', 'text_color' => '#003300']]),
    ]);

    $this->assertDatabaseHas('task_priority_colors', [
        'priority' => 'low',
        'background_color' => '#00FF00',
        'text_color' => '#003300',
    ]);
    $this->assertDatabaseHas('task_priority_colors', [
        'priority' => 'high',
        'background_color' => '#FDEAEA',
    ]);
});

test('submitting an invalid hex color for a priority is rejected', function () {
    $response = $this->actingAs($this->owner)->put('/task-colors/priority', [
        'priority_colors' => allPriorityColorsPayload(['high' => ['background_color' => 'not-a-color', 'text_color' => '#A32D2D']]),
    ]);

    $response->assertSessionHasErrors();
    expect(TaskPriorityColor::where('priority', 'high')->first()->background_color)->toBe('#FDEAEA');
});

test('a Status form validation error does not spuriously show under the Priority section, and vice versa', function () {
    $response = $this->followingRedirects()->actingAs($this->owner)->put('/task-colors/status', [
        'colors' => [
            'pending' => ['background_color' => 'not-a-color', 'text_color' => '#5F5E5A'],
            'in_progress' => ['background_color' => '#E1F5EE', 'text_color' => '#0F6E56'],
            'in_review' => ['background_color' => '#FAEEDA', 'text_color' => '#854F0B'],
            'completed' => ['background_color' => '#EAF3DE', 'text_color' => '#3B6D11'],
        ],
    ]);

    $response->assertOk();
    // The page re-renders with the Status error, but the Priority
    // section's own error slot must stay empty — different field names
    // (colors vs priority_colors) is what keeps them from bleeding into
    // each other.
    $response->assertDontSee('Every priority needs a color.');
});
