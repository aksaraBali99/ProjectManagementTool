<?php

use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

const TASK_QUERY_INDEXES = [
    'tasks_org_due_date_index' => ['organization_id', 'due_date'],
    'tasks_org_status_index' => ['organization_id', 'status'],
    'tasks_org_updated_at_index' => ['organization_id', 'updated_at'],
];

function taskIndexesMigration(): object
{
    return require database_path('migrations/2026_10_21_000000_add_task_query_indexes.php');
}

/** Index name => columns, in order, for the tasks table. */
function taskIndexColumns(): array
{
    return collect(Schema::getIndexes('tasks'))->mapWithKeys(fn ($i) => [$i['name'] => $i['columns']])->all();
}

/** Every tasks index this migration does not own. */
function otherTaskIndexes(): array
{
    return collect(taskIndexColumns())->except(array_keys(TASK_QUERY_INDEXES))->sortKeys()->all();
}

function taskForeignKeys(): array
{
    return collect(Schema::getForeignKeys('tasks'))->map(fn ($f) => [$f['columns'], $f['foreign_table']])->sort()->values()->all();
}

test('each task query index exists on the right columns in the right order', function () {
    $indexes = taskIndexColumns();

    foreach (TASK_QUERY_INDEXES as $name => $columns) {
        expect($indexes)->toHaveKey($name);
        expect($indexes[$name])->toBe($columns);
    }
});

test('rolling back removes only these indexes, keeps the foreign keys indexed, and re-applies cleanly', function () {
    $org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $dept = Department::create(['organization_id' => $org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $project = Project::create(['organization_id' => $org->id, 'name' => 'Project A', 'description' => 'd']);
    Task::create([
        'organization_id' => $org->id, 'project_id' => $project->id, 'department_id' => $dept->id,
        'title' => 'Task', 'priority' => 'high', 'status' => 'pending', 'due_date' => '2026-11-01',
    ]);

    $otherIndexesBefore = otherTaskIndexes();
    $foreignKeysBefore = taskForeignKeys();
    $rowsBefore = DB::table('tasks')->get()->map(fn ($r) => (array) $r)->all();

    taskIndexesMigration()->down();

    expect(array_keys(taskIndexColumns()))->not->toContain(...array_keys(TASK_QUERY_INDEXES));
    expect(otherTaskIndexes())->toBe($otherIndexesBefore);
    expect(taskForeignKeys())->toBe($foreignKeysBefore);
    expect(DB::table('tasks')->get()->map(fn ($r) => (array) $r)->all())->toBe($rowsBefore);

    // On MySQL the organization_id foreign key must still be backed by an
    // index after rollback (down() recreates the one MySQL dropped). SQLite
    // never indexes foreign keys, so there it is enough that nothing changed.
    if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
        expect(collect(taskIndexColumns())->contains(fn ($columns) => $columns[0] === 'organization_id'))->toBeTrue();
    }

    taskIndexesMigration()->up();

    foreach (TASK_QUERY_INDEXES as $name => $columns) {
        expect(taskIndexColumns()[$name] ?? null)->toBe($columns);
    }
    expect(DB::table('tasks')->get()->map(fn ($r) => (array) $r)->all())->toBe($rowsBefore);
});
