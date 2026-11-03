<?php

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Services\Import\ImportCommitService;
use App\Support\CompanyCode;
use Symfony\Component\Process\Process;

/**
 * task #118 phase 1: every task has a company-coded number (BGS-0012).
 * Numbers are per company, sequential, never reused, taken safely under
 * concurrency, and re-issued in the new company when a task moves.
 */
beforeEach(function () {
    $this->owner = createOwner();

    $this->bgs = Organization::create(['name' => 'Bali Goods Sourcing', 'slug' => 'bgs', 'code' => 'BGS', 'accent_color' => '#1D9E75']);
    $this->slv = Organization::create(['name' => 'Solava', 'slug' => 'slv', 'code' => 'SLV', 'accent_color' => '#534AB7']);

    foreach (['bgs', 'slv'] as $key) {
        $org = $this->{$key};
        $this->{$key.'Dept'} = Department::create(['organization_id' => $org->id, 'name' => 'Ops', 'color' => '#000000']);
        $this->{$key.'Project'} = Project::create(['organization_id' => $org->id, 'name' => 'Project '.$org->code, 'description' => 'd']);
    }
});

function makeCodedTask(object $context, string $company, array $attributes = []): Task
{
    return Task::create(array_merge([
        'organization_id' => $context->{$company}->id,
        'project_id' => $context->{$company.'Project'}->id,
        'department_id' => $context->{$company.'Dept'}->id,
        'title' => 'Task '.uniqid(),
        'priority' => 'medium',
        'status' => 'pending',
    ], $attributes));
}

test('tasks are numbered 1, 2, 3 within each company, with a separate counter per company', function () {
    $bgs = collect(range(1, 3))->map(fn () => makeCodedTask($this, 'bgs'));
    $slv = collect(range(1, 2))->map(fn () => makeCodedTask($this, 'slv'));
    $bgs->push(makeCodedTask($this, 'bgs'));

    expect($bgs->pluck('task_number')->all())->toBe([1, 2, 3, 4])
        ->and($slv->pluck('task_number')->all())->toBe([1, 2])
        ->and($bgs->map->code->all())->toBe(['BGS-0001', 'BGS-0002', 'BGS-0003', 'BGS-0004'])
        ->and($slv->map->code->all())->toBe(['SLV-0001', 'SLV-0002'])
        ->and($this->bgs->fresh()->next_task_number)->toBe(5)
        ->and($this->slv->fresh()->next_task_number)->toBe(3);
});

test('a deactivated task keeps its number, and it is never given out again', function () {
    makeCodedTask($this, 'bgs');
    $second = makeCodedTask($this, 'bgs');
    $second->delete();

    $third = makeCodedTask($this, 'bgs');

    expect($third->task_number)->toBe(3)
        ->and(Task::withTrashed()->find($second->id)->task_number)->toBe(2);

    $second->restore();
    expect($second->fresh()->task_number)->toBe(2);
});

test('the code is the company code, a hyphen and the number padded to four digits, never truncated', function (int $number, string $expected) {
    $this->slv->forceFill(['next_task_number' => $number])->save();

    expect(makeCodedTask($this, 'slv')->code)->toBe($expected);
})->with([
    'first' => [1, 'SLV-0001'],
    'four digits' => [9999, 'SLV-9999'],
    'five digits' => [10000, 'SLV-10000'],
    'six digits' => [123456, 'SLV-123456'],
]);

test('Task::formatCode() is the one format, and needs both parts', function () {
    expect(Task::formatCode('BGS', 12))->toBe('BGS-0012')
        ->and(Task::formatCode(null, 12))->toBeNull()
        ->and(Task::formatCode('BGS', null))->toBeNull();
});

test('moving a task to another company gives it that company\'s next number and logs the code change', function () {
    makeCodedTask($this, 'slv');
    makeCodedTask($this, 'slv');
    $task = collect(range(1, 3))->map(fn () => makeCodedTask($this, 'bgs'))->last();
    expect($task->code)->toBe('BGS-0003');

    $this->actingAs($this->owner);
    $task->update([
        'organization_id' => $this->slv->id,
        'project_id' => $this->slvProject->id,
        'department_id' => $this->slvDept->id,
    ]);

    $task->refresh();
    expect($task->task_number)->toBe(3)
        ->and($task->code)->toBe('SLV-0003')
        ->and($this->slv->fresh()->next_task_number)->toBe(4)
        // The old company's counter doesn't go back: BGS-0003 stays used.
        ->and($this->bgs->fresh()->next_task_number)->toBe(4)
        ->and(makeCodedTask($this, 'bgs')->code)->toBe('BGS-0004');

    $entry = AuditLog::where('entity_type', 'task')->where('entity_id', $task->id)->where('action', 'task.code_changed')->sole();
    expect($entry->changes)->toBe(['code' => ['old' => 'BGS-0003', 'new' => 'SLV-0003']])
        ->and($entry->organization_id)->toBe($this->slv->id)
        ->and($entry->describeChanges())->toContain('BGS-0003')->toContain('SLV-0003');

    // The move's own update entry has the company and project, but not a
    // bare task number: the code-changed entry tells that.
    $update = AuditLog::where('entity_id', $task->id)->where('action', 'task.updated')->sole();
    expect($update->changes)->toHaveKeys(['organization_id', 'project_id'])
        ->not->toHaveKey('task_number');
});

test('the "task created" audit entry records the number the task was created with', function () {
    $this->actingAs($this->owner);
    makeCodedTask($this, 'bgs');
    $task = makeCodedTask($this, 'bgs');

    $entry = AuditLog::where('entity_id', $task->id)->where('action', 'task.created')->sole();
    expect($entry->changes['task_number'])->toBe(2);
});

test('moving through the Edit Task form takes the new company\'s number too', function () {
    $task = makeCodedTask($this, 'bgs');
    makeCodedTask($this, 'slv');

    $this->actingAs($this->owner)->put(route('tasks.update', $task), [
        'project_id' => $this->slvProject->id,
        'department_id' => $this->slvDept->id,
        'title' => $task->title,
        'description' => 'd',
        'priority' => 'medium',
        'status' => 'pending',
    ])->assertRedirect();

    expect($task->fresh()->code)->toBe('SLV-0002')
        ->and(AuditLog::where('action', 'task.code_changed')->where('entity_id', $task->id)->value('changes'))
        ->toBe(['code' => ['old' => 'BGS-0001', 'new' => 'SLV-0002']]);
});

test('an edit that keeps the company keeps the number, with no code-changed entry', function () {
    $task = makeCodedTask($this, 'bgs');
    $this->actingAs($this->owner);

    $task->update(['title' => 'Renamed', 'status' => 'in_progress']);

    expect($task->fresh()->task_number)->toBe(1)
        ->and($this->bgs->fresh()->next_task_number)->toBe(2)
        ->and(AuditLog::where('action', 'task.code_changed')->exists())->toBeFalse();
});

test('tasks created by the Excel import get consecutive numbers after the company\'s existing ones', function () {
    makeCodedTask($this, 'bgs');
    makeCodedTask($this, 'bgs');

    $tasks = collect(range(1, 3))->map(fn (int $ref) => [
        'task_ref' => (string) $ref,
        'project_name' => $this->bgsProject->name,
        'company' => 'Bali Goods Sourcing',
        'department' => 'Ops',
        'title' => 'Imported '.$ref,
        'priority' => 'Medium',
        'status' => 'Pending',
    ])->all();

    $batch = runImportValidation(['Tasks' => $tasks], $this->owner);
    app(ImportCommitService::class)->commit($batch, $this->owner);

    $imported = Task::where('title', 'like', 'Imported %')->orderBy('id')->get();
    expect($imported->pluck('task_number')->all())->toBe([3, 4, 5])
        ->and($imported->map->code->all())->toBe(['BGS-0003', 'BGS-0004', 'BGS-0005'])
        ->and($this->bgs->fresh()->next_task_number)->toBe(6);
});

test('a company created without a code gets the default rule: first three letters, next free if taken', function () {
    $hire = Organization::create(['name' => 'Bali Hire', 'slug' => 'bali-hire', 'accent_color' => '#000000']);
    $another = Organization::create(['name' => 'Balinese Crafts', 'slug' => 'balinese', 'accent_color' => '#000000']);
    $given = Organization::create(['name' => 'Anything', 'slug' => 'anything', 'code' => 'ZZZ', 'accent_color' => '#000000']);

    expect($hire->code)->toBe('BAL')
        ->and($another->code)->toBe('BAI')
        ->and($given->code)->toBe('ZZZ')
        ->and($hire->fresh()->next_task_number)->toBe(1);
});

test('CompanyCode::suggest() tries the name\'s letters in order, then the alphabet', function () {
    expect(CompanyCode::suggest('Bali Hire', []))->toBe('BAL')
        ->and(CompanyCode::suggest('DiagOrg', ['DIA']))->toBe('DIG')
        ->and(CompanyCode::suggest('DiagOrg', ['DIA', 'DIG', 'DIO', 'DIR']))->toBe('DIB')
        ->and(CompanyCode::suggest('Él Café', []))->toBe('ELC')
        ->and(CompanyCode::suggest('A1', []))->toBe('AXX')
        ->and(CompanyCode::suggest('bali', ['bal']))->toBe('BAI')
        ->and(CompanyCode::isValid('BGS'))->toBeTrue()
        ->and(CompanyCode::isValid('bgs'))->toBeFalse()
        ->and(CompanyCode::isValid('BG1'))->toBeFalse()
        ->and(CompanyCode::isValid('BGSX'))->toBeFalse();
});

test('a company with no row cannot number a task', function () {
    expect(fn () => Organization::takeNextTaskNumber(999999))->toThrow(RuntimeException::class);
});

test('concurrent task creation in separate processes gives every task a different number, with no gaps', function () {
    // Real concurrency: four PHP processes create 15 tasks each at the
    // same time, against one SQLite file (the in-memory test database
    // can't be shared between processes).
    $database = tempnam(sys_get_temp_dir(), 'task-codes-').'.sqlite';
    touch($database);
    $env = [
        'APP_ENV' => 'testing',
        'DB_CONNECTION' => 'sqlite',
        'DB_DATABASE' => $database,
        'CACHE_STORE' => 'array',
        'SESSION_DRIVER' => 'array',
        'QUEUE_CONNECTION' => 'sync',
    ];
    $worker = __DIR__.'/fixtures/task-number-worker.php';

    try {
        $migrate = new Process([PHP_BINARY, base_path('artisan'), 'migrate', '--force'], base_path(), $env, null, 300);
        $migrate->mustRun();

        $setup = new Process([PHP_BINARY, $worker, base_path(), 'setup'], base_path(), $env, null, 60);
        $setup->mustRun();

        $workers = collect(range(1, 4))->map(function () use ($worker, $env) {
            $process = new Process([PHP_BINARY, $worker, base_path(), 'create', '15'], base_path(), $env, null, 120);
            $process->start();

            return $process;
        });
        $workers->each(function (Process $process) {
            $process->wait();
            expect($process->isSuccessful())->toBeTrue($process->getErrorOutput().$process->getOutput());
        });

        $pdo = new PDO('sqlite:'.$database);
        $numbers = $pdo->query('select task_number from tasks order by task_number')->fetchAll(PDO::FETCH_COLUMN);
        $next = (int) $pdo->query("select next_task_number from organizations where code = 'CCY'")->fetchColumn();

        expect(array_map('intval', $numbers))->toBe(range(1, 60))
            ->and($next)->toBe(61);
    } finally {
        $pdo = null;
        @unlink($database);
    }
});
