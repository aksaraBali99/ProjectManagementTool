<?php

use App\Models\Comment;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function longtextMigration(): object
{
    return require database_path('migrations/2026_09_21_000000_widen_task_description_and_comment_body_to_longtext.php');
}

/** Column name => nullable, for the two widened columns. */
function widenedColumnNullability(): array
{
    $nullable = fn (string $table, string $column) => collect(Schema::getColumns($table))->firstWhere('name', $column)['nullable'];

    return [
        'tasks.description' => $nullable('tasks', 'description'),
        'comments.body' => $nullable('comments', 'body'),
    ];
}

/** A table's structure apart from column types: column names, index names, foreign keys. */
function tableShape(string $table): array
{
    return [
        'columns' => collect(Schema::getColumns($table))->pluck('name')->sort()->values()->all(),
        'indexes' => collect(Schema::getIndexes($table))->map(fn ($i) => $i['columns'])->sort()->values()->all(),
        'foreign_keys' => collect(Schema::getForeignKeys($table))->map(fn ($f) => [$f['columns'], $f['foreign_table']])->sort()->values()->all(),
    ];
}

// The suite runs on SQLite, where the migration is deliberately a no-op (TEXT
// is already unbounded there, and Laravel's SQLite change() rebuilds the
// table). So this test can't exercise the MySQL ALTER itself (which is just
// `alter table ... modify ... longtext`, an in-place type change) — but it is
// the regression guard for the failure that actually happened without the
// driver check: rebuilding `tasks` fired ON DELETE CASCADE and silently
// deleted every comment.
test('the migration applies without touching existing rows, nullability, indexes or foreign keys', function () {
    $org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $dept = Department::create(['organization_id' => $org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $project = Project::create(['organization_id' => $org->id, 'name' => 'Project A', 'description' => 'd']);
    $user = User::factory()->create();

    // Start from the pre-migration (TEXT) shape, then seed "existing production data".
    longtextMigration()->down();
    $before = ['nullability' => widenedColumnNullability(), 'tasks' => tableShape('tasks'), 'comments' => tableShape('comments')];

    $legacyDescriptions = [
        'Plain description',
        "Multi-line\ndescription with <b>angle</b> & ampersand",
        null,
        '<p>Already-HTML looking</p>',
        str_repeat('x', 60000), // near TEXT's 64KB ceiling
    ];
    foreach ($legacyDescriptions as $i => $description) {
        $task = Task::create([
            'organization_id' => $org->id, 'project_id' => $project->id, 'department_id' => $dept->id,
            'title' => "Task {$i}", 'description' => $description, 'priority' => 'medium', 'status' => 'pending',
        ]);
        Comment::create(['task_id' => $task->id, 'user_id' => $user->id, 'body' => "Comment {$i}: ".($description ?? 'none')]);
    }
    $tasksBefore = DB::table('tasks')->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();
    $commentsBefore = DB::table('comments')->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();

    longtextMigration()->up();

    // Every row, every column — byte-for-byte identical (no transformation).
    expect(DB::table('tasks')->orderBy('id')->get()->map(fn ($r) => (array) $r)->all())->toBe($tasksBefore);
    expect(DB::table('comments')->orderBy('id')->get()->map(fn ($r) => (array) $r)->all())->toBe($commentsBefore);
    expect(count($tasksBefore))->toBe(5);

    // Nullability unchanged: description stays nullable, body stays NOT NULL.
    expect(widenedColumnNullability())->toBe($before['nullability']);
    expect(widenedColumnNullability())->toBe(['tasks.description' => true, 'comments.body' => false]);

    // Structure unchanged — the SQLite table rebuild / MySQL ALTER must not
    // have dropped an index or foreign key along the way.
    expect(tableShape('tasks'))->toBe($before['tasks']);
    expect(tableShape('comments'))->toBe($before['comments']);
});

test('after the migration a description and comment larger than TEXT could hold can be stored and read back', function () {
    $org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $dept = Department::create(['organization_id' => $org->id, 'name' => 'Marketing', 'color' => '#000000']);
    $project = Project::create(['organization_id' => $org->id, 'name' => 'Project A', 'description' => 'd']);
    $user = User::factory()->create();

    $large = '<p>'.str_repeat('0123456789', 10000).'</p>'; // ~100KB, over TEXT's 65,535 bytes

    $task = Task::create([
        'organization_id' => $org->id, 'project_id' => $project->id, 'department_id' => $dept->id,
        'title' => 'Big', 'description' => $large, 'priority' => 'medium', 'status' => 'pending',
    ]);
    $comment = Comment::create(['task_id' => $task->id, 'user_id' => $user->id, 'body' => $large]);

    expect($task->fresh()->description)->toBe($large);
    expect($comment->fresh()->body)->toBe($large);
});

test('the migration can be re-applied and rolled back cleanly', function () {
    longtextMigration()->down();
    longtextMigration()->up();
    longtextMigration()->up(); // widening an already-wide column is a no-op, not an error

    expect(widenedColumnNullability())->toBe(['tasks.description' => true, 'comments.body' => false]);
});
