<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

/**
 * SOL-0022 subtask 2: a due date can't be earlier than the start date, on
 * the task form and on subtasks. Only checked when a date changes, so a
 * task that already holds such dates (old data, or a task started late)
 * still saves its other fields.
 */
const DUE_BEFORE_START = "The due date can't be earlier than the start date.";

beforeEach(function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

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
        'title' => 'Task',
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
});

function dateRangeTaskPayload(Task $task, array $overrides = []): array
{
    return array_merge([
        'project_id' => $task->project_id,
        'department_id' => $task->department_id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => 'medium',
        'status' => $task->status->value,
        'start_date' => $task->start_date?->toDateString(),
        'due_date' => $task->due_date?->toDateString(),
    ], $overrides);
}

/** Writes dates straight to the row, skipping validation (old data). */
function storeTaskDates(Task $task, ?string $start, ?string $due): Task
{
    Task::withoutGlobalScopes()->whereKey($task->id)->update(['start_date' => $start, 'due_date' => $due]);

    return $task->fresh();
}

// -- Task form ----------------------------------------------------------

test('a task due date earlier than its start date is refused', function () {
    $this->actingAs($this->management)
        ->from("/tasks/{$this->task->id}/edit")
        ->put("/tasks/{$this->task->id}", dateRangeTaskPayload($this->task, ['start_date' => '2026-12-10', 'due_date' => '2026-12-05']))
        ->assertSessionHasErrors(['due_date' => DUE_BEFORE_START]);

    $task = $this->task->fresh();
    expect($task->start_date)->toBeNull();
    expect($task->due_date)->toBeNull();
});

test('a task due on its start date is saved', function () {
    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", dateRangeTaskPayload($this->task, ['start_date' => '2026-12-10', 'due_date' => '2026-12-10']))
        ->assertSessionHasNoErrors();

    expect($this->task->fresh()->due_date->toDateString())->toBe('2026-12-10');
});

test('a task with only one of the two dates is saved', function (array $dates) {
    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", dateRangeTaskPayload($this->task, $dates))
        ->assertSessionHasNoErrors();
})->with([
    'start only' => [['start_date' => '2026-12-10', 'due_date' => null]],
    'due only' => [['start_date' => null, 'due_date' => '2026-12-05']],
]);

test('a task already holding a due date before its start date still saves other changes', function () {
    $task = storeTaskDates($this->task, '2026-12-10', '2026-12-05');

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", dateRangeTaskPayload($task, ['title' => 'Renamed', 'status' => 'in_progress']))
        ->assertSessionHasNoErrors();

    $task = $task->fresh();
    expect($task->title)->toBe('Renamed');
    expect($task->start_date->toDateString())->toBe('2026-12-10');
    expect($task->due_date->toDateString())->toBe('2026-12-05');
});

test('changing such a task\'s due date to another day before the start is refused', function () {
    $task = storeTaskDates($this->task, '2026-12-10', '2026-12-05');

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", dateRangeTaskPayload($task, ['due_date' => '2026-12-07']))
        ->assertSessionHasErrors(['due_date' => DUE_BEFORE_START]);

    expect($task->fresh()->due_date->toDateString())->toBe('2026-12-05');
});

test('fixing such a task\'s dates is accepted, and so is clearing one', function (array $dates) {
    $task = storeTaskDates($this->task, '2026-12-10', '2026-12-05');

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", dateRangeTaskPayload($task, $dates))
        ->assertSessionHasNoErrors();
})->with([
    'due moved after start' => [['due_date' => '2026-12-12']],
    'due cleared' => [['due_date' => null]],
    'start cleared' => [['start_date' => null]],
]);

test('a malformed date only gets the date rule\'s error', function () {
    $response = $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", dateRangeTaskPayload($this->task, ['start_date' => '2026-12-10', 'due_date' => 'not-a-date']))
        ->assertSessionHasErrors('due_date');

    expect(session('errors')->get('due_date'))->not->toContain(DUE_BEFORE_START);
});

// -- Started late: the automatic start date ------------------------------

test('an overdue task moved to in progress on the form keeps start = today', function () {
    $task = storeTaskDates($this->task, null, now()->subDay()->toDateString());

    $this->actingAs($this->management)
        ->put("/tasks/{$task->id}", dateRangeTaskPayload($task, ['status' => 'in_progress']))
        ->assertSessionHasNoErrors();

    $task = $task->fresh();
    expect($task->status->value)->toBe('in_progress');
    expect($task->start_date->toDateString())->toBe(now()->toDateString());
});

test('an overdue task moved to in progress on the kanban keeps start = today', function () {
    $task = storeTaskDates($this->task, null, now()->subDay()->toDateString());

    $this->actingAs($this->management)
        ->patchJson("/tasks/{$task->id}/status", ['status' => 'in_progress'])
        ->assertOk();

    expect($task->fresh()->start_date->toDateString())->toBe(now()->toDateString());
});

// -- Subtasks -----------------------------------------------------------

test('a subtask start date after its stored due date is refused', function () {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'start_date' => '2026-12-01', 'due_date' => '2026-12-05']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['start_date' => '2026-12-10'])
        ->assertStatus(422)
        ->assertJsonPath('errors.start_date.0', DUE_BEFORE_START);

    expect($subtask->fresh()->start_date->toDateString())->toBe('2026-12-01');
});

test('a subtask due date before its stored start date is refused', function () {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'start_date' => '2026-12-10']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => '2026-12-05'])
        ->assertStatus(422)
        ->assertJsonPath('errors.due_date.0', DUE_BEFORE_START);

    expect($subtask->fresh()->due_date)->toBeNull();
});

test('clearing the due date of a subtask with an invalid pair is accepted', function () {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'start_date' => '2026-12-10', 'due_date' => '2026-12-05']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => null])
        ->assertOk();

    expect($subtask->fresh()->due_date)->toBeNull();
});

test('a subtask holding an invalid pair can still be renamed', function () {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'start_date' => '2026-12-10', 'due_date' => '2026-12-05']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['title' => 'Renamed'])
        ->assertOk();

    expect($subtask->fresh()->title)->toBe('Renamed');
});

test('adding a subtask that starts after it is due is refused; the same day is fine', function () {
    $this->actingAs($this->management)
        ->postJson("/tasks/{$this->task->id}/subtasks", ['title' => 'Bad', 'start_date' => '2026-12-10', 'due_date' => '2026-12-05'])
        ->assertStatus(422)
        ->assertJsonPath('errors.due_date.0', DUE_BEFORE_START);

    $this->actingAs($this->management)
        ->postJson("/tasks/{$this->task->id}/subtasks", ['title' => 'Good', 'start_date' => '2026-12-10', 'due_date' => '2026-12-10'])
        ->assertCreated();

    expect(Subtask::pluck('title')->all())->toBe(['Good']);
});

test('adding a subtask with the task\'s own dates is accepted even when they are an invalid pair', function () {
    // A task started late: the Add row starts out with these dates.
    $task = storeTaskDates($this->task, '2026-12-10', '2026-12-05');

    $this->actingAs($this->management)
        ->postJson("/tasks/{$task->id}/subtasks", ['title' => 'Sub', 'start_date' => '2026-12-10', 'due_date' => '2026-12-05'])
        ->assertCreated();
});

test('a malformed subtask date only gets the date rule\'s error', function () {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'start_date' => '2026-12-10']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => 'not-a-date'])
        ->assertStatus(422)
        ->assertJsonMissing(['due_date' => [DUE_BEFORE_START]]);
});
