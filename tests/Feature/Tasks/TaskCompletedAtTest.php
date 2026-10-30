<?php

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Services\Import\ImportCommitService;
use Illuminate\Support\Carbon;

/**
 * task #72 phase 6: TaskObserver::saving() keeps tasks.completed_at in step
 * with status — set on the way into Completed, cleared on the way out,
 * untouched otherwise — and the task page shows it.
 */
beforeEach(function () {
    $this->owner = createImportOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    $this->management = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $this->management->id,
        'role_id' => Role::where('slug', 'management')->firstOrFail()->id,
    ]);

    $this->travelTo(Carbon::parse('2026-10-12 09:30:00'));
});

function makeCompletedAtTask(object $context, string $status = 'pending', string $title = 'Task'): Task
{
    return Task::create([
        'organization_id' => $context->org->id,
        'project_id' => $context->project->id,
        'department_id' => $context->dept->id,
        'title' => $title,
        'description' => 'd',
        'priority' => 'medium',
        'status' => $status,
    ]);
}

/** The full task form, as it round-trips every field. */
function putCompletedAtTaskForm(object $context, Task $task, array $overrides = []): void
{
    $context->actingAs($context->management)->put("/tasks/{$task->id}", array_merge([
        'project_id' => $context->project->id,
        'department_id' => $context->dept->id,
        'title' => $task->title,
        'description' => $task->description,
        'priority' => 'medium',
        'status' => $task->status->value,
        'start_date' => $task->start_date?->toDateString(),
    ], $overrides))->assertRedirect();
}

test('a task created as Completed gets completed_at now', function () {
    $task = makeCompletedAtTask($this, 'completed');

    expect($task->fresh()->completed_at->toDateTimeString())->toBe('2026-10-12 09:30:00');
});

test('a task created as anything else has no completed_at', function (string $status) {
    expect(makeCompletedAtTask($this, $status)->fresh()->completed_at)->toBeNull();
})->with(['pending', 'in_progress', 'in_review']);

test('moving to Completed sets it, moving away clears it, completing again takes the new time', function () {
    $task = makeCompletedAtTask($this);

    $task->update(['status' => 'completed']);
    expect($task->fresh()->completed_at->toDateTimeString())->toBe('2026-10-12 09:30:00');

    $this->travelTo(Carbon::parse('2026-10-14 08:00:00'));
    $task->update(['status' => 'in_review']);
    expect($task->fresh()->completed_at)->toBeNull();

    $this->travelTo(Carbon::parse('2026-10-20 16:45:00'));
    $task->update(['status' => 'completed']);
    expect($task->fresh()->completed_at->toDateTimeString())->toBe('2026-10-20 16:45:00');
});

test('editing a completed task without changing its status leaves completed_at alone', function () {
    $task = makeCompletedAtTask($this, 'completed');

    $this->travelTo(Carbon::parse('2026-10-25 10:00:00'));
    putCompletedAtTaskForm($this, $task, ['title' => 'Renamed', 'priority' => 'high']);

    $task->refresh();
    expect($task->title)->toBe('Renamed')
        ->and($task->completed_at->toDateTimeString())->toBe('2026-10-12 09:30:00');
});

test('deactivating and reactivating a completed task leaves completed_at alone', function () {
    $task = makeCompletedAtTask($this, 'completed');

    $this->travelTo(Carbon::parse('2026-10-25 10:00:00'));
    $this->actingAs($this->management)->patch(route('tasks.toggle-active', $task))->assertRedirect();
    expect(Task::withTrashed()->find($task->id)->completed_at->toDateTimeString())->toBe('2026-10-12 09:30:00');

    $this->travelTo(Carbon::parse('2026-10-26 10:00:00'));
    $this->actingAs($this->management)->patch(route('tasks.toggle-active', $task))->assertRedirect();
    expect($task->fresh()->completed_at->toDateTimeString())->toBe('2026-10-12 09:30:00');
});

test('the task form sets it, and its audit row carries completed_at next to status', function () {
    $task = makeCompletedAtTask($this);

    putCompletedAtTaskForm($this, $task, ['status' => 'completed']);

    expect($task->fresh()->completed_at->toDateTimeString())->toBe('2026-10-12 09:30:00');

    $entry = AuditLog::where('entity_id', $task->id)->where('action', 'task.status_changed')->firstOrFail();
    expect($entry->changes['status'])->toBe(['old' => 'pending', 'new' => 'completed'])
        ->and($entry->changes['completed_at']['old'])->toBeNull()
        ->and($entry->changes['completed_at']['new'])->toStartWith('2026-10-12 09:30:00');
});

test('the audit text shows completed_at as a readable date on completion and on reopen', function () {
    $task = makeCompletedAtTask($this);

    $this->actingAs($this->management)->patchJson(route('tasks.update-status', $task), ['status' => 'completed'])->assertOk();
    $this->travelTo(Carbon::parse('2026-10-14 15:05:00'));
    $this->actingAs($this->management)->patchJson(route('tasks.update-status', $task), ['status' => 'in_progress'])->assertOk();

    [$completion, $reopen] = AuditLog::where('entity_id', $task->id)->where('action', 'task.status_changed')->orderBy('id')->get();

    // The reopen's old value is stored ISO-8601 (from the cast Carbon),
    // the completion's new value as "Y-m-d H:i:s" — both read the same.
    expect($reopen->changes['completed_at']['old'])->toContain('T');

    expect($completion->describeChanges())->toBe('Status changed from Pending to Completed; Completed At changed from — to Oct 12, 2026 9:30am')
        ->and($reopen->describeChanges())->toContain('Completed At changed from Oct 12, 2026 9:30am to —')
        ->and($reopen->describeChanges())->not->toContain('000000Z');
});

test('a Kanban status change sets it and clears it', function () {
    $task = makeCompletedAtTask($this);

    $this->actingAs($this->management)->patchJson(route('tasks.update-status', $task), ['status' => 'completed'])->assertOk();
    expect($task->fresh()->completed_at->toDateTimeString())->toBe('2026-10-12 09:30:00');

    $this->actingAs($this->management)->patchJson(route('tasks.update-status', $task), ['status' => 'pending'])->assertOk();
    expect($task->fresh()->completed_at)->toBeNull();
});

test('an Import creating a completed task sets it', function () {
    runImportValidationAndCommit($this, 'Completed');

    $task = Task::withoutGlobalScopes()->where('title', 'Imported task')->firstOrFail();
    expect($task->completed_at->toDateTimeString())->toBe('2026-10-12 09:30:00');
});

test('an Import that does not complete the task leaves it null', function () {
    runImportValidationAndCommit($this, 'Pending');

    expect(Task::withoutGlobalScopes()->where('title', 'Imported task')->firstOrFail()->completed_at)->toBeNull();
});

function runImportValidationAndCommit(object $context, string $status): void
{
    $batch = runImportValidation([
        'Tasks' => [[
            'task_ref' => '1',
            'project_name' => 'Project A',
            'company' => 'Org A',
            'department' => 'Marketing',
            'title' => 'Imported task',
            'priority' => 'Medium',
            'status' => $status,
        ]],
    ], $context->owner);

    $context->actingAs($context->owner);
    app(ImportCommitService::class)->commit($batch, $context->owner);
}

test('the task page shows "Completed on" for a completed task, in the page date style', function () {
    $task = makeCompletedAtTask($this, 'completed');

    $this->actingAs($this->management)->get(route('tasks.edit', $task))->assertOk()
        ->assertSee('Completed on Oct 12, 2026');
});

test('the read-only task page shows it too', function () {
    $task = makeCompletedAtTask($this, 'completed');

    // A client of the project sees the task but can't edit it.
    $client = User::factory()->create();
    OrgMember::create([
        'organization_id' => $this->org->id,
        'user_id' => $client->id,
        'role_id' => Role::where('slug', 'client')->firstOrFail()->id,
    ]);
    $this->project->clients()->attach($client->id);

    $response = $this->actingAs($client)->get(route('tasks.edit', $task))->assertOk();
    expect($response->getContent())->not->toContain('name="status"')
        ->toMatch('/Completed on<\/span><p[^>]*>Oct 12, 2026<\/p>/');
});

test('the task page shows nothing for a task that is not completed', function () {
    $task = makeCompletedAtTask($this);
    $task->update(['status' => 'completed']);
    $task->update(['status' => 'in_progress']);

    $this->actingAs($this->management)->get(route('tasks.edit', $task))->assertOk()
        ->assertDontSee('Completed on');
});
