<?php

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\NotificationSetting;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;

/**
 * SOL-0022: solava:fix-task-dates corrects existing rows that break the
 * date rules. Dry run by default; --apply --as=<owner> saves the changes
 * as that user (audited), without notifications, one transaction per task.
 */
beforeEach(function () {
    $this->owner = createOwner(['username' => 'olive']);
    $this->org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $this->dept = Department::create(['organization_id' => $this->org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $this->project = Project::create(['organization_id' => $this->org->id, 'name' => 'Project A', 'description' => 'd']);

    File::deleteDirectory(storage_path('app/datafix'));
});

afterEach(function () {
    File::deleteDirectory(storage_path('app/datafix'));
});

/** A task with these dates, written straight to the row (old data skips validation). */
function fixDatesTask(Project $project, Department $dept, string $title, ?string $start, ?string $due, string $status = 'pending'): Task
{
    $task = Task::create([
        'organization_id' => $project->organization_id,
        'project_id' => $project->id,
        'department_id' => $dept->id,
        'title' => $title,
        'priority' => 'medium',
        'status' => $status,
    ]);
    Task::withoutGlobalScopes()->whereKey($task->id)->update(['start_date' => $start, 'due_date' => $due]);

    return Task::withoutGlobalScopes()->withTrashed()->find($task->id);
}

function fixDatesSubtask(Task $task, string $title, ?string $start, ?string $due): Subtask
{
    return $task->subtasks()->create(['title' => $title, 'start_date' => $start, 'due_date' => $due]);
}

function dueOf(Task|Subtask $model): ?string
{
    $fresh = $model instanceof Task ? Task::withoutGlobalScopes()->withTrashed()->find($model->id) : $model->fresh();

    return $fresh->due_date?->toDateString();
}

/** The three kinds of bad row, in one company. */
function fixDatesScenario(Project $project, Department $dept): array
{
    $withBadSubtask = fixDatesTask($project, $dept, 'Has a backwards subtask', '2026-12-01', '2026-12-20');

    return [
        'withBadSubtask' => $withBadSubtask,
        'badSubtask' => fixDatesSubtask($withBadSubtask, 'Backwards subtask', '2026-12-10', '2026-12-05'),
        'badTask' => fixDatesTask($project, $dept, 'Backwards task', '2026-12-10', '2026-12-05'),
        'lateSubtaskTask' => $lateSubtaskTask = fixDatesTask($project, $dept, 'Due before its subtask', '2026-12-01', '2026-12-05'),
        'lateSubtask' => fixDatesSubtask($lateSubtaskTask, 'Runs late', '2026-12-01', '2026-12-15'),
    ];
}

// -- Dry run ------------------------------------------------------------------

test('a dry run lists every change and changes nothing', function () {
    $rows = fixDatesScenario($this->project, $this->dept);
    $auditRows = AuditLog::count();

    $this->artisan('solava:fix-task-dates')
        // One expectation per printed line: the table lists task codes.
        ->expectsOutputToContain('Backwards subtask')
        ->expectsOutputToContain($rows['badTask']->code)
        ->expectsOutputToContain($rows['lateSubtaskTask']->code)
        ->expectsOutputToContain('3 changes on 3 tasks: 1 subtask due date, 2 task due dates.')
        ->expectsOutputToContain('Dry run: nothing changed. Run again with --apply --as=<username> to make these changes.')
        ->assertSuccessful();

    expect(dueOf($rows['badSubtask']))->toBe('2026-12-05');
    expect(dueOf($rows['badTask']))->toBe('2026-12-05');
    expect(dueOf($rows['lateSubtaskTask']))->toBe('2026-12-05');
    expect(AuditLog::count())->toBe($auditRows);
    expect(File::isDirectory(storage_path('app/datafix')))->toBeFalse();
});

// -- Who it runs as -------------------------------------------------------------

test('--apply without --as is refused and changes nothing', function () {
    $rows = fixDatesScenario($this->project, $this->dept);

    $this->artisan('solava:fix-task-dates', ['--apply' => true])
        ->expectsOutputToContain('--apply needs --as=<username or email>')
        ->assertFailed();

    expect(dueOf($rows['badTask']))->toBe('2026-12-05');
});

test('--apply is refused for an unknown, inactive, staff or management user', function (string $kind) {
    $rows = fixDatesScenario($this->project, $this->dept);
    $as = 'nobody';

    if ($kind !== 'unknown') {
        $user = User::factory()->create(['username' => $kind, 'is_active' => $kind !== 'inactive']);
        if ($kind === 'inactive') {
            $user->roles()->attach(Role::where('slug', 'owner')->value('id'));
        } else {
            OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $user->id, 'role_id' => Role::where('slug', $kind)->value('id')]);
        }
        $as = $kind;
    }

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => $as])->assertFailed();

    expect(dueOf($rows['badTask']))->toBe('2026-12-05');
})->with(['unknown', 'inactive', 'staff', 'management']);

// -- Applying -------------------------------------------------------------------

test('--apply fixes every kind of row, audited as the owner, with no notifications, and writes a CSV', function () {
    Notification::fake();
    $rows = fixDatesScenario($this->project, $this->dept);
    // Step 2 then step 3 on one task: due before start, and a later subtask.
    $both = fixDatesTask($this->project, $this->dept, 'Backwards with a later subtask', '2026-12-10', '2026-12-05');
    fixDatesSubtask($both, 'Later still', '2026-12-10', '2026-12-18');
    // Step 1 creates work for step 3: the fixed subtask ends after its task.
    $chain = fixDatesTask($this->project, $this->dept, 'Pushed by a fixed subtask', '2026-12-01', '2026-12-08');
    fixDatesSubtask($chain, 'Backwards and late', '2026-12-12', '2026-12-02');
    $lastAuditId = AuditLog::max('id') ?? 0;

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'olive'])
        ->expectsOutputToContain('Report: ')
        ->assertSuccessful();

    expect(dueOf($rows['badSubtask']))->toBe('2026-12-10');
    expect(dueOf($rows['badTask']))->toBe('2026-12-10');
    expect(dueOf($rows['lateSubtaskTask']))->toBe('2026-12-15');
    expect(dueOf($both))->toBe('2026-12-18');
    expect(dueOf($chain))->toBe('2026-12-12');

    // One audit row per change, all by the owner: 1 + 1 + 1 (scenario),
    // 2 (step 2 and step 3 on $both), 2 (subtask, then its task).
    $newAudit = AuditLog::where('id', '>', $lastAuditId)->get();
    expect($newAudit)->toHaveCount(7);
    expect($newAudit->pluck('user_id')->unique()->all())->toBe([$this->owner->id]);

    Notification::assertNothingSent();

    $csv = File::files(storage_path('app/datafix'));
    expect($csv)->toHaveCount(1);
    $lines = array_filter(explode("\n", trim(File::get($csv[0]->getPathname()))));
    expect($lines)->toHaveCount(1 + 7);
    expect($lines[1])->toContain('olive');
});

test('a deactivated task is fixed and stays deactivated; a completed task keeps its status and completed_at', function () {
    $inactive = fixDatesTask($this->project, $this->dept, 'Inactive', '2026-12-10', '2026-12-05');
    fixDatesSubtask($inactive, 'Under an inactive task', '2026-12-08', '2026-12-06');
    $inactive->delete();

    $completed = fixDatesTask($this->project, $this->dept, 'Done', '2026-12-10', '2026-12-05', 'completed');
    $completedAt = Task::withoutGlobalScopes()->find($completed->id)->completed_at;

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'olive'])->assertSuccessful();

    $inactive = Task::withoutGlobalScopes()->withTrashed()->find($inactive->id);
    expect($inactive->trashed())->toBeTrue();
    expect($inactive->due_date->toDateString())->toBe('2026-12-10');
    expect($inactive->subtasks()->first()->due_date->toDateString())->toBe('2026-12-08');

    $completed = Task::withoutGlobalScopes()->find($completed->id);
    expect($completed->due_date->toDateString())->toBe('2026-12-10');
    expect($completed->status->value)->toBe('completed');
    expect($completed->completed_at?->toDateTimeString())->toBe($completedAt?->toDateTimeString());
});

test('rows that keep the rules are left alone, and a subtask with no task is reported and skipped', function () {
    $sameDay = fixDatesTask($this->project, $this->dept, 'Same day', '2026-12-10', '2026-12-10');
    $oneDate = fixDatesTask($this->project, $this->dept, 'Only a due date', null, '2026-12-05');
    $noDue = fixDatesTask($this->project, $this->dept, 'No due date', '2026-12-01', null);
    fixDatesSubtask($noDue, 'Any time', '2026-12-01', '2027-03-01');
    $early = fixDatesTask($this->project, $this->dept, 'Subtask starts early', '2026-12-10', '2026-12-20');
    fixDatesSubtask($early, 'Starts early', '2026-12-01', '2026-12-15');

    // A subtask pointing at a task that doesn't exist. The test's own
    // transaction is rolled back, so the deferred foreign key check never
    // runs (SQLite ignores turning foreign keys off inside a transaction).
    $orphan = fixDatesSubtask($sameDay, 'Orphan', '2026-12-10', '2026-12-05');
    DB::statement('PRAGMA defer_foreign_keys = ON');
    DB::table('subtasks')->where('id', $orphan->id)->update(['task_id' => 999999]);

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'olive'])
        ->expectsOutputToContain("Skipped: subtask #{$orphan->id} \"Orphan\" has no task (task #999999).")
        ->expectsOutputToContain('Nothing to fix.')
        ->assertSuccessful();

    expect(dueOf($sameDay))->toBe('2026-12-10');
    expect(dueOf($oneDate))->toBe('2026-12-05');
    expect(dueOf($noDue))->toBeNull();
    expect(dueOf($early))->toBe('2026-12-20');
    expect($orphan->fresh()->due_date->toDateString())->toBe('2026-12-05');
});

test('a second --apply finds nothing to fix and writes no audit rows', function () {
    fixDatesScenario($this->project, $this->dept);
    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'olive'])->assertSuccessful();
    $auditRows = AuditLog::count();

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'olive'])
        ->expectsOutputToContain('Nothing to fix.')
        ->assertSuccessful();

    expect(AuditLog::count())->toBe($auditRows);
});

test('after the command, notifications are sent again in the same process', function () {
    fixDatesScenario($this->project, $this->dept);
    $recipient = User::factory()->create();
    $recipient->roles()->attach(Role::where('slug', 'owner')->value('id'));
    NotificationSetting::create([
        'owner_id' => $recipient->id,
        'event_type' => 'task_status_changed',
        'channel' => 'in_app',
        'recipients' => ['type' => 'users', 'ids' => [$recipient->id]],
        'is_active' => true,
    ]);

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'olive'])->assertSuccessful();

    expect(app()->bound('datafix.running'))->toBeFalse();
    expect(auth()->check())->toBeFalse();

    // A due date change has no notification type (NotificationEventType),
    // so a status change proves the flag is gone.
    $this->actingAs($this->owner);
    Task::withoutGlobalScopes()->where('title', 'Backwards task')->first()->update(['status' => 'in_progress']);

    expect($recipient->fresh()->notifications()->count())->toBe(1);
});

// -- Second review ---------------------------------------------------------------

test('--as matches a username before an email', function () {
    fixDatesScenario($this->project, $this->dept);
    $this->owner->update(['email' => 'chief@example.test']);
    // Another user whose username is the owner's email.
    $staff = User::factory()->create(['username' => 'chief@example.test']);
    OrgMember::create(['organization_id' => $this->org->id, 'user_id' => $staff->id, 'role_id' => Role::where('slug', 'staff')->value('id')]);

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'chief@example.test'])
        ->expectsOutputToContain("isn't an owner or super admin")
        ->assertFailed();

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'olive'])->assertSuccessful();
});

test('when the CSV can\'t be written, the changes still stand and it says so', function () {
    $rows = fixDatesScenario($this->project, $this->dept);
    // A file where the report folder should be.
    File::put(storage_path('app/datafix'), 'in the way');

    $this->artisan('solava:fix-task-dates', ['--apply' => true, '--as' => 'olive'])
        ->expectsOutputToContain("The changes above were made, but the CSV report couldn't be written")
        ->assertSuccessful();

    expect(dueOf($rows['badTask']))->toBe('2026-12-10');
    File::delete(storage_path('app/datafix'));
});
