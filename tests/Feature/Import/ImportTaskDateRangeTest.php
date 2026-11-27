<?php

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\ImportRow;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Services\Import\ImportCommitService;

/**
 * SOL-0022 subtask 2 (B): Bulk Import follows the same date rules. Due
 * before start is an error row (a subtask on the dates it will actually
 * get, a blank one copied from its task). A subtask due after its task
 * can't be confirmed during an import, so the task's due date is moved
 * out on commit, and the review page warns about it first.
 */
beforeEach(function () {
    $this->owner = createImportOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
});

function dateRangeImportTask(array $overrides = []): array
{
    return array_merge([
        'task_ref' => '1',
        'project_name' => 'Project A',
        'company' => 'Org A',
        'department' => 'Marketing',
        'title' => 'Launch page',
        'priority' => 'Medium',
        'status' => 'Pending',
    ], $overrides);
}

function importRow($batch, string $sheet): ImportRow
{
    return $batch->importRows()->where('sheet_name', $sheet)->orderBy('row_number')->firstOrFail();
}

// -- Tasks sheet ------------------------------------------------------------

test('a task row due before its start date is an error, and its subtasks are blocked', function () {
    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['start_date' => '10/12/2026', 'due_date' => '05/12/2026'])],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub']],
    ], $this->owner);

    $task = importRow($batch, 'Tasks');
    expect($task->validation_status->value)->toBe('error');
    expect($task->validation_message)->toBe("The due date can't be earlier than the start date.");

    expect(importRow($batch, 'Subtasks')->resolved_action->value)->toBe('blocked');
});

test('a task row due on its start date is valid', function () {
    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['start_date' => '10/12/2026', 'due_date' => '10/12/2026'])],
    ], $this->owner);

    expect(importRow($batch, 'Tasks')->validation_status->value)->toBe('valid');
});

// -- Subtasks sheet -----------------------------------------------------------

test('a subtask row due before its own start date is an error', function () {
    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask()],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub', 'start_date' => '10/12/2026', 'due_date' => '05/12/2026']],
    ], $this->owner);

    $row = importRow($batch, 'Subtasks');
    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toBe("The due date can't be earlier than the start date.");
});

test('a subtask row is checked on the start date it copies from its task', function () {
    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['start_date' => '10/12/2026', 'due_date' => '20/12/2026'])],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub', 'due_date' => '05/12/2026']],
    ], $this->owner);

    $row = importRow($batch, 'Subtasks');
    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('A blank date is copied from its task (Ref 1).');
});

test('a subtask row due on its start date is valid', function () {
    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['due_date' => '20/12/2026'])],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub', 'start_date' => '10/12/2026', 'due_date' => '10/12/2026']],
    ], $this->owner);

    expect(importRow($batch, 'Subtasks')->validation_status->value)->toBe('valid');
});

// -- Subtask due after its task: moved out on import -------------------------

test('subtasks due after their task warn, and the commit moves the task to the latest one', function () {
    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['due_date' => '05/12/2026'])],
        'Subtasks' => [
            ['task_ref' => '1', 'title' => 'On time', 'due_date' => '01/12/2026'],
            ['task_ref' => '1', 'title' => 'Late', 'due_date' => '10/12/2026'],
            ['task_ref' => '1', 'title' => 'Later', 'due_date' => '12/12/2026'],
        ],
    ], $this->owner);

    $rows = $batch->importRows()->where('sheet_name', 'Subtasks')->orderBy('row_number')->get();
    expect($rows->pluck('validation_status')->map->value->all())->toBe(['valid', 'warning', 'warning']);
    expect($rows[1]->validation_message)->toBe("This subtask is due after its task: the task's due date will move from 5 Dec 2026 to 12 Dec 2026.");

    $this->actingAs($this->owner);
    app(ImportCommitService::class)->commit($batch, $this->owner);

    $task = Task::withoutGlobalScopes()->where('title', 'Launch page')->firstOrFail();
    expect($task->due_date->toDateString())->toBe('2026-12-12');

    $audit = AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->where('action', 'task.updated')->latest('id')->firstOrFail();
    expect($audit->import_batch_id)->toBe($batch->fresh()->uuid);
    expect($audit->changes['due_date']['new'])->toStartWith('2026-12-12');
});

test('a task with no due date is not given one on import', function () {
    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask()],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub', 'due_date' => '10/12/2026']],
    ], $this->owner);

    expect(importRow($batch, 'Subtasks')->validation_status->value)->toBe('valid');

    $this->actingAs($this->owner);
    app(ImportCommitService::class)->commit($batch, $this->owner);

    expect(Task::withoutGlobalScopes()->where('title', 'Launch page')->firstOrFail()->due_date)->toBeNull();
});

test('a task row moving an existing task before its existing subtasks warns, and the commit moves it out', function () {
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Launch page',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => '2026-12-31',
    ]);
    $task->subtasks()->create(['title' => 'Existing', 'due_date' => '2026-12-20']);

    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['due_date' => '05/12/2026'])],
    ], $this->owner);

    $row = importRow($batch, 'Tasks');
    expect($row->validation_status->value)->toBe('warning');
    expect($row->validation_message)->toBe("Existing subtasks are due after this date: the task's due date will move to 20 Dec 2026 to fit them.");

    $this->actingAs($this->owner);
    app(ImportCommitService::class)->commit($batch, $this->owner);

    expect($task->fresh()->due_date->toDateString())->toBe('2026-12-20');
});

// -- Review fixes -------------------------------------------------------------

test('re-importing a task with its own stored start-after-due dates is not an error', function () {
    // A task started late: moved to In Progress after its due date.
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Launch page',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    Task::withoutGlobalScopes()->whereKey($task->id)->update(['start_date' => '2026-11-27', 'due_date' => '2026-11-20']);

    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['priority' => 'High', 'start_date' => '27/11/2026', 'due_date' => '20/11/2026'])],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub', 'start_date' => '18/11/2026', 'due_date' => '19/11/2026']],
    ], $this->owner);

    expect(importRow($batch, 'Tasks')->validation_status->value)->not->toBe('error');
    expect(importRow($batch, 'Subtasks')->resolved_action->value)->toBe('insert');
});

test('changing such a task\'s dates on import is still checked', function () {
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Launch page',
        'priority' => 'medium',
        'status' => 'pending',
    ]);
    Task::withoutGlobalScopes()->whereKey($task->id)->update(['start_date' => '2026-11-27', 'due_date' => '2026-11-20']);

    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['start_date' => '27/11/2026', 'due_date' => '21/11/2026'])],
    ], $this->owner);

    expect(importRow($batch, 'Tasks')->validation_status->value)->toBe('error');
});

test('a subtask copying the start date an Active (in progress) task gets today is checked against today', function () {
    $this->travelTo(now()->setDate(2026, 11, 27));

    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['status' => 'Active', 'due_date' => '01/01/2026'])],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub', 'due_date' => '05/01/2026']],
    ], $this->owner);

    $row = importRow($batch, 'Subtasks');
    expect($row->validation_status->value)->toBe('error');
    expect($row->validation_message)->toContain('A blank date is copied from its task (Ref 1).');
});

test('a task already Active (in progress) keeps a blank start on import, so its subtasks copy no start', function () {
    $this->travelTo(now()->setDate(2026, 11, 27));
    Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Launch page',
        'priority' => 'medium',
        'status' => 'in_progress',
    ]);
    Task::withoutGlobalScopes()->where('title', 'Launch page')->update(['start_date' => null]);

    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['status' => 'Active', 'due_date' => '01/01/2026'])],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub', 'due_date' => '01/01/2026']],
    ], $this->owner);

    expect(importRow($batch, 'Subtasks')->validation_status->value)->toBe('valid');
});

// -- Second review ---------------------------------------------------------------

test('a subtask copying both dates from a task started late is imported, like the Add row', function () {
    $this->travelTo(now()->setDate(2026, 11, 27));

    $batch = runImportValidation([
        // Active with a blank start: it starts today, after its due date.
        'Tasks' => [dateRangeImportTask(['status' => 'Active', 'due_date' => '20/11/2026'])],
        'Subtasks' => [['task_ref' => '1', 'title' => 'Sub']],
    ], $this->owner);

    expect(importRow($batch, 'Subtasks')->validation_status->value)->toBe('valid');
});

test('a task row repeating its stored values is an update when its due date will move out', function (string $source) {
    $task = Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Launch page',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => '2026-12-05',
    ]);
    if ($source === 'existing subtask') {
        $task->subtasks()->create(['title' => 'Existing', 'due_date' => '2026-12-10']);
    }

    $batch = runImportValidation([
        'Tasks' => [dateRangeImportTask(['due_date' => '05/12/2026'])],
        'Subtasks' => $source === 'subtask in the file' ? [['task_ref' => '1', 'title' => 'New', 'due_date' => '10/12/2026']] : [],
    ], $this->owner);

    expect(importRow($batch, 'Tasks')->resolved_action->value)->toBe('update');
})->with(['existing subtask', 'subtask in the file']);

test('a task row repeating its stored values, with nothing moving, stays no_change', function () {
    Task::create([
        'organization_id' => $this->org->id,
        'project_id' => $this->project->id,
        'department_id' => $this->dept->id,
        'title' => 'Launch page',
        'priority' => 'medium',
        'status' => 'pending',
        'due_date' => '2026-12-05',
    ]);

    $batch = runImportValidation(['Tasks' => [dateRangeImportTask(['due_date' => '05/12/2026'])]], $this->owner);

    expect(importRow($batch, 'Tasks')->resolved_action->value)->toBe('no_change');
});
