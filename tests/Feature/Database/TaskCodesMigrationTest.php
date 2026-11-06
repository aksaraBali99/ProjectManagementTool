<?php

use App\Models\Task;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * task #118 phase 1: the migration that gives companies their codes and
 * numbers existing tasks. Each test rolls it back, writes pre-migration
 * rows directly, runs up() again and checks the result.
 */
beforeEach(function () {
    $this->migration = require database_path('migrations/2026_11_03_000000_add_task_codes.php');
    $this->migration->down();

    $this->company = function (string $name, bool $active = true): int {
        return DB::table('organizations')->insertGetId([
            'name' => $name, 'slug' => str()->slug($name).'-'.uniqid(), 'accent_color' => '#000000',
            'is_active' => $active, 'created_at' => now(), 'updated_at' => now(),
        ]);
    };

    // A task row as it was before task_number existed.
    $this->task = function (int $organizationId, array $attributes = []): int {
        $project = DB::table('projects')->where('organization_id', $organizationId)->value('id') ?? DB::table('projects')->insertGetId([
            'organization_id' => $organizationId, 'name' => 'P', 'description' => 'd', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $department = DB::table('departments')->where('organization_id', $organizationId)->value('id') ?? DB::table('departments')->insertGetId([
            'organization_id' => $organizationId, 'name' => 'D', 'color' => '#000000', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return DB::table('tasks')->insertGetId(array_merge([
            'organization_id' => $organizationId, 'project_id' => $project, 'department_id' => $department,
            'title' => 'T', 'priority' => 'medium', 'status' => 'pending', 'created_at' => now(), 'updated_at' => now(),
        ], $attributes));
    };
});

afterEach(function () {
    $this->app['env'] = 'testing';
});

function inProduction(): void
{
    app()['env'] = 'production';
}

test('production: each mapped company gets its code, and its tasks are numbered oldest first, deactivated companies and tasks included', function () {
    inProduction();
    $bgs = ($this->company)('Bali Goods Sourcing');
    $slv = ($this->company)('Solava');
    $bva = ($this->company)('Bali Virtual Academy', active: false);
    $ava = ($this->company)('Aksara Virtual Agency');
    ($this->company)('English Hub');

    // Interleaved across companies, so each company's own order is by id,
    // not a global count. One soft-deleted task keeps its place.
    $order = [
        [$bgs, 'b1'], [$slv, 's1'], [$bgs, 'b2'], [$bva, 'v1'], [$bgs, 'b3'], [$slv, 's2'], [$bva, 'v2'],
    ];
    $ids = [];
    foreach ($order as [$organizationId, $key]) {
        $ids[$key] = ($this->task)($organizationId, $key === 'b2' ? ['deleted_at' => now()] : []);
    }
    // A child row, to prove the migration rebuilds no table (a SQLite
    // rebuild would cascade-delete it).
    DB::table('subtasks')->insert(['task_id' => $ids['b3'], 'title' => 'Child', 'is_done' => false, 'created_at' => now(), 'updated_at' => now()]);

    $this->migration->up();

    $numbers = DB::table('tasks')->pluck('task_number', 'id');
    expect($numbers[$ids['b1']])->toBe(1)->and($numbers[$ids['b2']])->toBe(2)->and($numbers[$ids['b3']])->toBe(3)
        ->and($numbers[$ids['s1']])->toBe(1)->and($numbers[$ids['s2']])->toBe(2)
        ->and($numbers[$ids['v1']])->toBe(1)->and($numbers[$ids['v2']])->toBe(2);

    $organizations = DB::table('organizations')->get()->keyBy('name');
    expect($organizations->map->code->all())->toBe([
        'Bali Goods Sourcing' => 'BGS', 'Solava' => 'SOL', 'Bali Virtual Academy' => 'BVA', 'Aksara Virtual Agency' => 'AVA', 'English Hub' => 'EHU',
    ])
        ->and((int) $organizations['Bali Goods Sourcing']->next_task_number)->toBe(4)
        ->and((int) $organizations['Solava']->next_task_number)->toBe(3)
        ->and((int) $organizations['Bali Virtual Academy']->next_task_number)->toBe(3)
        // No tasks: the counter starts at 1.
        ->and((int) $organizations['Aksara Virtual Agency']->next_task_number)->toBe(1)
        ->and(DB::table('subtasks')->where('task_id', $ids['b3'])->count())->toBe(1);

    // Every company's highest number is its task count, and the next
    // number follows it.
    foreach ([$bgs, $slv, $bva, $ava] as $organizationId) {
        $count = DB::table('tasks')->where('organization_id', $organizationId)->count();
        expect((int) DB::table('tasks')->where('organization_id', $organizationId)->max('task_number'))->toBe($count)
            ->and((int) DB::table('organizations')->where('id', $organizationId)->value('next_task_number'))->toBe($count + 1);
    }
    expect($ava)->toBeInt();
});

test('after the migration, new tasks continue from the backfilled numbers', function () {
    $bgs = ($this->company)('Bali Goods Sourcing');
    ($this->task)($bgs);
    ($this->task)($bgs);

    $this->migration->up();

    $task = Task::create([
        'organization_id' => $bgs,
        'project_id' => DB::table('projects')->where('organization_id', $bgs)->value('id'),
        'department_id' => DB::table('departments')->where('organization_id', $bgs)->value('id'),
        'title' => 'New', 'priority' => 'medium', 'status' => 'pending',
    ]);

    expect($task->code)->toBe('BGS-0003');
});

test('production: a company missing from the map stops the migration, listing every such company, deactivated ones included, and changes nothing', function () {
    inProduction();
    ($this->company)('Bali Goods Sourcing');
    ($this->company)('Bali Hire');
    ($this->company)('Old Venture', active: false);

    expect(fn () => $this->migration->up())->toThrow(
        RuntimeException::class,
        "these companies have no code in the map: 'Bali Hire' (id ",
    );

    try {
        $this->migration->up();
    } catch (RuntimeException $e) {
        expect($e->getMessage())->toContain("'Old Venture'")->toContain('deactivated');
    }

    expect(Schema::hasColumn('organizations', 'code'))->toBeFalse()
        ->and(Schema::hasColumn('tasks', 'task_number'))->toBeFalse();
});

test('an invalid code in the map stops the migration', function (string $code) {
    $this->migration->codes = ['Bali Goods Sourcing' => $code];
    ($this->company)('Bali Goods Sourcing');

    expect(fn () => $this->migration->up())->toThrow(RuntimeException::class, "the map's code '{$code}' for 'Bali Goods Sourcing' is not three uppercase letters");
    expect(Schema::hasColumn('organizations', 'code'))->toBeFalse();
})->with(['lowercase' => ['bgs'], 'too long' => ['BGSS'], 'too short' => ['BG'], 'digit' => ['BG5']]);

test('a code given to two companies in the map stops the migration', function () {
    $this->migration->codes = ['Bali Goods Sourcing' => 'BGS', 'Bali Garden Supply' => 'BGS'];
    ($this->company)('Bali Goods Sourcing');

    expect(fn () => $this->migration->up())->toThrow(RuntimeException::class, "the map gives 'BGS' to more than one company: 'Bali Goods Sourcing', 'Bali Garden Supply'");
});

test('two companies with the same mapped name would share a code, which stops the migration', function () {
    inProduction();
    ($this->company)('Solava');
    ($this->company)('Solava');

    expect(fn () => $this->migration->up())->toThrow(RuntimeException::class, "these codes would be shared by more than one company: SOL ('Solava', 'Solava')");
    expect(Schema::hasColumn('organizations', 'code'))->toBeFalse();
});

test('outside production, unmapped companies get the suggested code, or the next free one', function () {
    ($this->company)('Bali Virtual Academy');
    ($this->company)('Bali Hire');
    ($this->company)('DiagOrg');
    ($this->company)('DiagOrg');
    ($this->company)('DiagOrg2');
    // Its suggestion is SOL, the map's code for Solava: reserved
    // even though Solava isn't here, so it gets the next free one.
    ($this->company)('Solid Oak Lumber');

    $this->migration->up();

    // task #118 phase 2's suggestion rule (CompanyCode::suggest()).
    expect(DB::table('organizations')->orderBy('id')->pluck('code')->all())->toBe(['BVA', 'BAH', 'DGR', 'DIA', 'DIG', 'SOI']);
});

test('down() removes the columns and indexes and keeps the tasks and their children', function () {
    $bgs = ($this->company)('Bali Goods Sourcing');
    $task = ($this->task)($bgs);
    DB::table('subtasks')->insert(['task_id' => $task, 'title' => 'Child', 'is_done' => false, 'created_at' => now(), 'updated_at' => now()]);
    $this->migration->up();

    $this->migration->down();

    expect(Schema::hasColumn('organizations', 'code'))->toBeFalse()
        ->and(Schema::hasColumn('organizations', 'next_task_number'))->toBeFalse()
        ->and(Schema::hasColumn('tasks', 'task_number'))->toBeFalse()
        ->and(DB::table('tasks')->count())->toBe(1)
        ->and(DB::table('subtasks')->count())->toBe(1);

    // And it re-applies cleanly.
    $this->migration->up();
    expect(DB::table('tasks')->value('task_number'))->toBe(1);
});

test('after a failure part-way, running up() again finishes the job instead of dying on "duplicate column"', function () {
    $bgs = ($this->company)('Bali Goods Sourcing');
    $slv = ($this->company)('Solava');
    ($this->task)($bgs);
    ($this->task)($slv);
    ($this->task)($bgs);

    // The state a failure after the first steps would leave on MySQL
    // (each schema change commits on its own): the company columns and
    // the task column exist, some codes and numbers are set, no indexes.
    Schema::table('organizations', function ($table) {
        $table->char('code', 3)->nullable();
        $table->unsignedInteger('next_task_number')->default(1);
    });
    Schema::table('tasks', function ($table) {
        $table->unsignedInteger('task_number')->nullable();
    });
    DB::table('organizations')->where('id', $bgs)->update(['code' => 'BGS']);
    DB::table('tasks')->where('organization_id', $bgs)->orderBy('id')->limit(1)->update(['task_number' => 1]);

    $this->migration->up();

    expect(DB::table('organizations')->orderBy('id')->pluck('code')->all())->toBe(['BGS', 'SOL'])
        ->and(DB::table('tasks')->orderBy('id')->pluck('task_number')->all())->toBe([1, 1, 2])
        ->and((int) DB::table('organizations')->where('id', $bgs)->value('next_task_number'))->toBe(3)
        ->and(Schema::hasIndex('organizations', ['code'], 'unique'))->toBeTrue()
        ->and(Schema::hasIndex('tasks', ['organization_id', 'task_number'], 'unique'))->toBeTrue();

    // And once more on a finished database: nothing to do, nothing breaks.
    $this->migration->up();
    expect(DB::table('tasks')->orderBy('id')->pluck('task_number')->all())->toBe([1, 1, 2]);
});

test('numbering works across chunks: 1,203 tasks in one company are 1..1,203 in id order', function () {
    $bgs = ($this->company)('Bali Goods Sourcing');
    $slv = ($this->company)('Solava');
    ($this->task)($slv);
    $project = DB::table('projects')->insertGetId(['organization_id' => $bgs, 'name' => 'P', 'description' => 'd', 'created_at' => now(), 'updated_at' => now()]);
    $department = DB::table('departments')->insertGetId(['organization_id' => $bgs, 'name' => 'D', 'color' => '#000000', 'created_at' => now(), 'updated_at' => now()]);
    foreach (array_chunk(range(1, 1203), 300) as $chunk) {
        DB::table('tasks')->insert(array_map(fn () => [
            'organization_id' => $bgs, 'project_id' => $project, 'department_id' => $department,
            'title' => 'T', 'priority' => 'medium', 'status' => 'pending', 'created_at' => now(), 'updated_at' => now(),
        ], $chunk));
    }

    $this->migration->up();

    expect(DB::table('tasks')->where('organization_id', $bgs)->orderBy('id')->pluck('task_number')->all())->toBe(range(1, 1203))
        ->and(DB::table('tasks')->where('organization_id', $slv)->value('task_number'))->toBe(1)
        ->and((int) DB::table('organizations')->where('id', $bgs)->value('next_task_number'))->toBe(1204);
});

test('the unique indexes are in place: no two companies share a code, no two tasks in a company share a number', function () {
    $bgs = ($this->company)('Bali Goods Sourcing');
    ($this->task)($bgs);
    $this->migration->up();

    expect(fn () => DB::table('organizations')->insert([
        'name' => 'Copy', 'slug' => 'copy', 'code' => 'BGS', 'accent_color' => '#000000', 'created_at' => now(), 'updated_at' => now(),
    ]))->toThrow(UniqueConstraintViolationException::class);

    expect(fn () => ($this->task)($bgs, ['task_number' => 1]))->toThrow(UniqueConstraintViolationException::class);
});
