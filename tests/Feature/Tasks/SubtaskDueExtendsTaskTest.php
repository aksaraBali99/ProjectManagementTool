<?php

use App\Models\AuditLog;
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
 * SOL-0022 subtask 2 (A): a subtask due after its task. Without the
 * extend_task_due_date flag the server refuses and nothing is saved; with
 * it, the subtask is saved and the task's due date moves out to its latest
 * subtask's. Moving the task's own due date earlier than a subtask's is
 * refused.
 */
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
        'due_date' => '2026-12-05',
    ]);
});

function extendTaskPayload(Task $task, array $overrides = []): array
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

// -- Subtask PUT ----------------------------------------------------------

test('a subtask due after its task is refused without the flag, and nothing is saved', function () {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'due_date' => '2026-12-01']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => '2026-12-10'])
        ->assertStatus(422)
        ->assertJsonPath('needs_task_due_extension', true)
        ->assertJsonPath('new_task_due_date', '2026-12-10')
        ->assertJsonPath('errors.due_date.0', "This subtask is due after its task (5 Dec 2026). Continue to move the task's due date to 10 Dec 2026.");

    expect($subtask->fresh()->due_date->toDateString())->toBe('2026-12-01');
    expect($this->task->fresh()->due_date->toDateString())->toBe('2026-12-05');
});

test('with the flag, the subtask is saved and the task moves out, with an audit row', function () {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'due_date' => '2026-12-01']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => '2026-12-10', 'extend_task_due_date' => true])
        ->assertOk()
        ->assertJsonPath('task_due_date', '2026-12-10');

    expect($subtask->fresh()->due_date->toDateString())->toBe('2026-12-10');
    expect($this->task->fresh()->due_date->toDateString())->toBe('2026-12-10');

    $audit = AuditLog::where('entity_type', 'task')->where('entity_id', $this->task->id)->latest('id')->firstOrFail();
    expect($audit->user_id)->toBe($this->management->id);
    expect($audit->changes['due_date']['new'])->toStartWith('2026-12-10');
});

test('the task moves out to its latest subtask, not just the one being saved', function () {
    // Old data: a subtask already due after the task.
    $this->task->subtasks()->create(['title' => 'Later', 'due_date' => '2026-12-20']);
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'due_date' => '2026-12-01']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => '2026-12-10'])
        ->assertStatus(422)
        ->assertJsonPath('new_task_due_date', '2026-12-20');

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => '2026-12-10', 'extend_task_due_date' => true])
        ->assertOk()
        ->assertJsonPath('task_due_date', '2026-12-20');
});

test('a subtask due on or before its task needs no confirmation', function () {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => '2026-12-05'])
        ->assertOk()
        ->assertJsonPath('task_due_date', '2026-12-05');
});

test('a task with no due date has no limit and stays without one', function () {
    Task::withoutGlobalScopes()->whereKey($this->task->id)->update(['due_date' => null]);
    $subtask = $this->task->subtasks()->create(['title' => 'Sub']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => '2027-06-01'])
        ->assertOk();

    expect($this->task->fresh()->due_date)->toBeNull();
});

test('a completed task is moved out like any other', function () {
    $this->task->update(['status' => 'completed']);
    $subtask = $this->task->subtasks()->create(['title' => 'Sub']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", ['due_date' => '2026-12-10', 'extend_task_due_date' => true])
        ->assertOk();

    $task = $this->task->fresh();
    expect($task->due_date->toDateString())->toBe('2026-12-10');
    expect($task->status->value)->toBe('completed');
});

test('saving anything but the due date of a subtask already due after its task is not checked', function (array $payload) {
    $subtask = $this->task->subtasks()->create(['title' => 'Sub', 'due_date' => '2026-12-20']);

    $this->actingAs($this->management)
        ->putJson("/subtasks/{$subtask->id}", $payload)
        ->assertOk();

    expect($this->task->fresh()->due_date->toDateString())->toBe('2026-12-05');
})->with([
    'title' => [['title' => 'Renamed']],
    'start date' => [['start_date' => '2026-12-01']],
    'same due date again' => [['due_date' => '2026-12-20']],
]);

// -- Subtask POST ---------------------------------------------------------

test('adding a subtask due after its task needs the flag', function () {
    $this->actingAs($this->management)
        ->postJson("/tasks/{$this->task->id}/subtasks", ['title' => 'Sub', 'due_date' => '2026-12-10'])
        ->assertStatus(422)
        ->assertJsonPath('needs_task_due_extension', true);

    expect(Subtask::count())->toBe(0);

    $this->actingAs($this->management)
        ->postJson("/tasks/{$this->task->id}/subtasks", ['title' => 'Sub', 'due_date' => '2026-12-10', 'extend_task_due_date' => true])
        ->assertCreated()
        ->assertJsonPath('task_due_date', '2026-12-10');

    expect($this->task->fresh()->due_date->toDateString())->toBe('2026-12-10');
});

// -- Edit Task: the task's own due date -------------------------------------

test('moving the task\'s due date earlier than its latest subtask is refused', function () {
    $this->task->subtasks()->create(['title' => 'Sub', 'due_date' => '2026-12-04']);

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", extendTaskPayload($this->task, ['due_date' => '2026-12-03']))
        ->assertSessionHasErrors(['due_date' => 'Some subtasks are due after this date. Move them first, or choose 4 Dec 2026 or later.']);

    expect($this->task->fresh()->due_date->toDateString())->toBe('2026-12-05');
});

test('the task\'s due date can move to its latest subtask\'s day, or be cleared', function (?string $due) {
    $this->task->subtasks()->create(['title' => 'Sub', 'due_date' => '2026-12-04']);

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", extendTaskPayload($this->task, ['due_date' => $due]))
        ->assertSessionHasNoErrors();
})->with(['same day' => '2026-12-04', 'cleared' => null]);

test('a task already due before a subtask still saves when its due date is untouched', function () {
    $this->task->subtasks()->create(['title' => 'Sub', 'due_date' => '2026-12-20']);

    $this->actingAs($this->management)
        ->put("/tasks/{$this->task->id}", extendTaskPayload($this->task, ['title' => 'Renamed']))
        ->assertSessionHasNoErrors();

    expect($this->task->fresh()->title)->toBe('Renamed');
});

// -- Add Task ---------------------------------------------------------------

function addTaskPayload(Project $project, Department $dept, array $overrides = []): array
{
    return array_merge([
        'project_id' => $project->id,
        'department_id' => $dept->id,
        'title' => 'New task',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => '2026-12-05',
        'subtasks' => [
            ['title' => 'Early', 'assignee_id' => '', 'due_date' => '2026-12-01'],
            ['title' => 'Late', 'assignee_id' => '', 'due_date' => '2026-12-12'],
        ],
    ], $overrides);
}

test('Add Task with a subtask due after the task is refused without the flag', function () {
    $this->actingAs($this->management)
        ->post('/tasks', addTaskPayload($this->project, $this->dept))
        ->assertSessionHasErrors(['due_date' => "A subtask is due after this task (5 Dec 2026). Continue to move the task's due date to 12 Dec 2026."]);

    expect(Task::withoutGlobalScopes()->where('title', 'New task')->exists())->toBeFalse();
});

test('Add Task with the flag creates the task due on its latest subtask', function () {
    $this->actingAs($this->management)
        ->post('/tasks', addTaskPayload($this->project, $this->dept, ['extend_task_due_date' => '1']))
        ->assertSessionHasNoErrors();

    $task = Task::withoutGlobalScopes()->where('title', 'New task')->firstOrFail();
    expect($task->due_date->toDateString())->toBe('2026-12-12');
    expect($task->subtasks()->count())->toBe(2);
});

test('Add Task with no task due date has no limit', function () {
    $this->actingAs($this->management)
        ->post('/tasks', addTaskPayload($this->project, $this->dept, ['due_date' => null]))
        ->assertSessionHasNoErrors();

    expect(Task::withoutGlobalScopes()->where('title', 'New task')->firstOrFail()->due_date)->toBeNull();
});

test('Add Task shows the refusal under its Due date field', function () {
    $this->actingAs($this->management)
        ->from('/tasks/create')
        ->followingRedirects()
        ->post('/tasks', addTaskPayload($this->project, $this->dept))
        ->assertSeeInOrder(['id="due_date"', 'A subtask is due after this task (5 Dec 2026).', '>Subtasks<'], false);
});
