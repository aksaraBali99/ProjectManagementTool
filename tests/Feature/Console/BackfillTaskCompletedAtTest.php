<?php

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 6: `tasks:backfill-completed-at` — one test per history
 * shape. Tasks are created through Eloquent with nobody signed in (so no
 * audit rows), then completed_at is wiped to look like a pre-phase-6 row,
 * and the history is hand-built in exactly the shapes TaskObserver (and
 * the pre-observer controller code) wrote.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);
});

/** A task as it looked before phase 6: no completed_at, a known updated_at. */
function makeLegacyTask(object $context, string $status, string $updatedAt = '2026-09-30 12:00:00', ?Organization $organization = null): Task
{
    $organization ??= $context->org;
    $department = $organization->is($context->org) ? $context->dept : Department::create(['organization_id' => $organization->id, 'name' => 'Ops', 'color' => '#000000']);
    $project = $organization->is($context->org) ? $context->project : Project::create(['organization_id' => $organization->id, 'name' => 'Project B', 'description' => 'd']);

    $task = Task::create([
        'organization_id' => $organization->id,
        'project_id' => $project->id,
        'department_id' => $department->id,
        'title' => 'Task',
        'priority' => 'medium',
        'status' => $status,
    ]);
    DB::table('tasks')->where('id', $task->id)->update(['completed_at' => null, 'updated_at' => $updatedAt]);

    return $task;
}

function addTaskAudit(object $context, Task $task, string $action, array $changes, string $at, string $entityType = 'task'): void
{
    DB::table('audit_log')->insert([
        'organization_id' => $task->organization_id,
        'user_id' => $context->owner->id,
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $task->id,
        'changes' => json_encode($changes),
        'created_at' => $at,
    ]);
}

/**
 * Runs the command and returns its exit code, output, and the summary
 * table as [label => count].
 *
 * @return array{exit: int, output: string, rows: array<string, int>}
 */
function runBackfill(array $options = []): array
{
    $exit = Artisan::call('tasks:backfill-completed-at', $options);
    $output = Artisan::output();
    preg_match_all('/^\|\s*(.+?)\s*\|\s*(\d+)\s*\|\r?$/m', $output, $matches, PREG_SET_ORDER);

    return ['exit' => $exit, 'output' => $output, 'rows' => collect($matches)->mapWithKeys(fn ($m) => [$m[1] => (int) $m[2]])->all()];
}

function completedAtOf(Task $task): ?string
{
    return DB::table('tasks')->where('id', $task->id)->value('completed_at');
}

function flatCreated(string $status): array
{
    return ['project_id' => 1, 'department_id' => 1, 'assignee_id' => null, 'title' => 'Task', 'priority' => 'medium', 'status' => $status, 'due_date' => null];
}

test('completed once: takes that status change', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.created', flatCreated('pending'), '2026-09-01 08:00:00');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:15:00');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Filled from task.status_changed'] ?? null)->toBe(1);

    expect(completedAtOf($task))->toBe('2026-09-05 10:15:00');
});

test('completed, reopened, completed again: takes the last completion', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.created', flatCreated('pending'), '2026-09-01 08:00:00');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:00:00');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'completed', 'new' => 'in_progress']], '2026-09-07 10:00:00');
    addTaskAudit($this, $task, 'task.updated', ['title' => ['old' => 'a', 'new' => 'b']], '2026-09-08 10:00:00');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'in_progress', 'new' => 'completed']], '2026-09-12 17:30:00');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);

    expect(completedAtOf($task))->toBe('2026-09-12 17:30:00');
});

test('created already completed: takes the flat task.created row', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.created', flatCreated('completed'), '2026-09-02 09:00:00');
    addTaskAudit($this, $task, 'task.updated', ['title' => ['old' => 'a', 'new' => 'b']], '2026-09-20 09:00:00');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Filled from task.created'] ?? null)->toBe(1);

    expect(completedAtOf($task))->toBe('2026-09-02 09:00:00');
});

test('completed in a save that also changed the assignee: takes the task.reassigned row', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.created', flatCreated('in_progress'), '2026-09-01 08:00:00');
    addTaskAudit($this, $task, 'task.reassigned', [
        'status' => ['old' => 'in_progress', 'new' => 'completed'],
        'assignee_id' => ['old' => null, 'new' => $this->owner->id],
    ], '2026-09-09 14:00:00');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Filled from task.reassigned or another action carrying a status change'] ?? null)->toBe(1);

    expect(completedAtOf($task))->toBe('2026-09-09 14:00:00');
});

test('an imported completion (source=import) counts like any other', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.created', flatCreated('completed') + ['source' => 'import'], '2026-09-03 11:00:00');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);

    expect(completedAtOf($task))->toBe('2026-09-03 11:00:00');
});

test('the pre-observer App\Models\Task shape (flat new value) is read too', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'created', flatCreated('pending'), '2026-08-19 08:00:00', 'App\\Models\\Task');
    addTaskAudit($this, $task, 'updated', ['status' => 'completed'], '2026-08-20 13:00:00', 'App\\Models\\Task');

    $created = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $created, 'created', flatCreated('completed'), '2026-08-19 09:00:00', 'App\\Models\\Task');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Filled from task.created'] ?? null)->toBe(1);
    // Its own line, not lumped in with task.reassigned.
    expect($run['rows']['Filled from a pre-observer status change (before Aug 2026)'] ?? null)->toBe(1);
    expect($run['rows']['Filled from task.reassigned or another action carrying a status change'] ?? null)->toBe(0);

    expect(completedAtOf($task))->toBe('2026-08-20 13:00:00')
        ->and(completedAtOf($created))->toBe('2026-08-19 09:00:00');
});

test('no audit rows at all: falls back to updated_at', function () {
    $task = makeLegacyTask($this, 'completed', '2026-09-30 18:20:00');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Filled from the updated_at fallback'] ?? null)->toBe(1);

    expect(completedAtOf($task))->toBe('2026-09-30 18:20:00');
});

test('history ending away from Completed (a later unlogged change) falls back to updated_at', function () {
    $task = makeLegacyTask($this, 'completed', '2026-09-30 18:20:00');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:00:00');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'completed', 'new' => 'pending']], '2026-09-06 10:00:00');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);

    expect(completedAtOf($task))->toBe('2026-09-30 18:20:00');
});

test('a task that is not completed now stays null, whatever its history', function () {
    $task = makeLegacyTask($this, 'in_review');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:00:00');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'completed', 'new' => 'in_review']], '2026-09-06 10:00:00');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Completed tasks checked'] ?? null)->toBe(0);

    expect(completedAtOf($task))->toBeNull();
});

test('an inactive completed task is backfilled the same way', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:15:00');
    DB::table('tasks')->where('id', $task->id)->update(['deleted_at' => '2026-09-10 00:00:00']);

    $run = runBackfill();
    expect($run['exit'])->toBe(0);

    expect(completedAtOf($task))->toBe('2026-09-05 10:15:00');
});

test('--dry-run writes nothing but reports what it would do', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:15:00');

    $run = runBackfill(['--dry-run' => true]);
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Would fill from task.status_changed'] ?? null)->toBe(1);
    expect($run['output'])->toContain('Dry run: nothing was written.');

    expect(completedAtOf($task))->toBeNull();
});

test('by default an existing completed_at is kept; --force recomputes it', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:15:00');
    DB::table('tasks')->where('id', $task->id)->update(['completed_at' => '2026-10-01 00:00:00']);

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Skipped, already set'] ?? null)->toBe(1);
    expect($run['rows']['Total written'] ?? null)->toBe(0);
    expect(completedAtOf($task))->toBe('2026-10-01 00:00:00');

    $run = runBackfill(['--force' => true]);
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Total written'] ?? null)->toBe(1);
    expect(completedAtOf($task))->toBe('2026-09-05 10:15:00');

    // Already correct now, so a second --force writes nothing.
    $run = runBackfill(['--force' => true]);
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Already correct (--force)'] ?? null)->toBe(1);
    expect($run['rows']['Total written'] ?? null)->toBe(0);
});

test('--organization limits it to one company', function () {
    $other = Organization::create(['name' => 'Org B', 'slug' => 'org-b', 'accent_color' => '#000000']);
    $mine = makeLegacyTask($this, 'completed');
    $theirs = makeLegacyTask($this, 'completed', organization: $other);

    $run = runBackfill(['--organization' => $this->org->id]);
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Completed tasks checked'] ?? null)->toBe(1);

    expect(completedAtOf($mine))->not->toBeNull()
        ->and(completedAtOf($theirs))->toBeNull();
});

test('--organization with an unknown company fails without writing', function () {
    $task = makeLegacyTask($this, 'completed');

    $run = runBackfill(['--organization' => 999999]);
    expect($run['exit'])->toBe(1);

    expect(completedAtOf($task))->toBeNull();
});

test('a second run changes nothing', function () {
    $task = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:15:00');
    makeLegacyTask($this, 'completed');

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Total written'] ?? null)->toBe(2);

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Completed tasks checked'] ?? null)->toBe(2);
    expect($run['rows']['Skipped, already set'] ?? null)->toBe(2);
    expect($run['rows']['Total written'] ?? null)->toBe(0);
});

test('it writes no per-task audit rows, sends nothing and leaves updated_at alone', function () {
    $task = makeLegacyTask($this, 'completed', '2026-09-30 18:20:00');
    addTaskAudit($this, $task, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:15:00');
    $taskAuditRows = DB::table('audit_log')->where('entity_type', 'task')->count();

    // Signed in, so a model event WOULD write an audit row.
    $this->actingAs($this->owner);
    $run = runBackfill();
    expect($run['exit'])->toBe(0);

    // Only the run's own summary row is new.
    expect(DB::table('audit_log')->where('entity_type', 'task')->count())->toBe($taskAuditRows)
        ->and(DB::table('audit_log')->where('action', 'tasks.completed_at_backfilled')->count())->toBe(1)
        ->and(DB::table('notifications')->count())->toBe(0)
        ->and(DB::table('tasks')->where('id', $task->id)->value('updated_at'))->toBe('2026-09-30 18:20:00')
        ->and(completedAtOf($task))->toBe('2026-09-05 10:15:00');
});

test('it works across chunk boundaries', function () {
    $tasks = collect(range(1, 501))->map(fn () => makeLegacyTask($this, 'completed', '2026-09-30 18:20:00'));

    $run = runBackfill();
    expect($run['exit'])->toBe(0);
    expect($run['rows']['Total written'] ?? null)->toBe(501);

    expect(DB::table('tasks')->whereIn('id', $tasks->pluck('id'))->whereNull('completed_at')->count())->toBe(0);
});

test('a real run writes one summary audit row with no user, readable in the Audit Trail', function () {
    $completed = makeLegacyTask($this, 'completed');
    addTaskAudit($this, $completed, 'task.status_changed', ['status' => ['old' => 'pending', 'new' => 'completed']], '2026-09-05 10:15:00');
    makeLegacyTask($this, 'completed');

    runBackfill(['--organization' => $this->org->id]);

    $entry = AuditLog::where('action', 'tasks.completed_at_backfilled')->sole();
    expect($entry->user_id)->toBeNull()
        ->and($entry->organization_id)->toBe($this->org->id)
        ->and($entry->entity_type)->toBe('command')
        ->and($entry->changes)->toMatchArray([
            'source' => 'cli_backfill',
            'triggered_from' => 'tasks:backfill-completed-at',
            'tasks_checked' => 2,
            'tasks_written' => 2,
            'from_status_changed' => 1,
            'from_updated_at_fallback' => 1,
            'left_empty' => 0,
        ])
        ->and($entry->entityLabel())->toBe('tasks:backfill-completed-at')
        ->and($entry->describeChanges())->toContain('Tasks Written: 2');

    $this->actingAs($this->owner)->get(route('audit-trail.index'))->assertOk()
        ->assertSee('Tasks Completed At Backfilled')
        ->assertSee('tasks:backfill-completed-at')
        ->assertSee('Tasks Written: 2');
});

test('--force is recorded in the summary row', function () {
    makeLegacyTask($this, 'completed');

    runBackfill(['--force' => true]);

    expect(AuditLog::where('action', 'tasks.completed_at_backfilled')->sole()->changes['triggered_from'])
        ->toBe('tasks:backfill-completed-at --force');
});

test('no summary audit row for a dry run, or for a run that writes nothing', function () {
    makeLegacyTask($this, 'completed');

    runBackfill(['--dry-run' => true]);
    expect(AuditLog::where('action', 'tasks.completed_at_backfilled')->count())->toBe(0);

    runBackfill();
    runBackfill();
    expect(AuditLog::where('action', 'tasks.completed_at_backfilled')->count())->toBe(1);
});

test('a task with no history and no timestamps is left empty, not reported as filled', function () {
    $task = makeLegacyTask($this, 'completed');
    DB::table('tasks')->where('id', $task->id)->update(['updated_at' => null, 'created_at' => null]);

    $run = runBackfill();

    expect($run['rows']['Left empty, no date available'] ?? null)->toBe(1)
        ->and($run['rows']['Filled from the updated_at fallback'] ?? null)->toBe(0)
        ->and($run['rows']['Total written'] ?? null)->toBe(0)
        ->and(completedAtOf($task))->toBeNull()
        ->and(AuditLog::where('action', 'tasks.completed_at_backfilled')->count())->toBe(0);
});
