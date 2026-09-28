<?php

use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;

function removeClientViewDocumentsMigration(): object
{
    return require database_path('migrations/2026_09_28_000001_remove_client_view_documents_permission.php');
}

/**
 * The migration itself (2026_09_28_000000_add_upload_columns_to_documents_
 * table.php) already ran once during this suite's initial migrate — every
 * test in this file, like every other Feature test, runs against the
 * fully-migrated schema. That's deliberate: unlike LongtextMigrationTest
 * (which skips its own ->change() entirely on SQLite, since there's
 * nothing to widen there), this migration's new columns genuinely need to
 * exist under Pest's SQLite suite too, so it can't skip there.
 *
 * What CAN'T be done here is literally replay ->down()/->up() mid-test to
 * verify the migration doesn't lose data on an upgrade, the way
 * LongtextMigrationTest's non-SQLite path effectively does — Pest's
 * RefreshDatabase wraps every test in its own transaction, and SQLite
 * silently ignores a `PRAGMA foreign_keys` change made INSIDE an open
 * transaction, so Schema::disableForeignKeyConstraints() (this
 * migration's own cascade-delete guard, see its docblock) can't actually
 * take effect when called this way — a replay here would show a false
 * failure that has nothing to do with the real migration path. That real
 * path (a genuine `php artisan migrate`/`migrate:rollback`, or a fresh
 * install) was verified empirically instead, against a real scratch
 * SQLite database seeded with pre-migration-shape data, outside any test
 * transaction — see the project's local-verification-tooling memory.
 *
 * So this file instead verifies the thing the TESTS list actually asks
 * for: that an existing, pre-Phase-1-shaped document (no storage_key,
 * only `link`) keeps rendering correctly under the now-migrated schema,
 * and that its task attachment is unaffected by anything this phase adds.
 */
test('an existing link-only document and its task attachment render correctly under the migrated schema', function () {
    $org = Organization::create(['name' => 'Org A', 'slug' => 'org-a', 'accent_color' => '#1D9E75']);
    $uploader = User::factory()->create();
    $project = Project::create(['organization_id' => $org->id, 'name' => 'Project A', 'description' => 'd']);
    $dept = Department::create(['organization_id' => $org->id, 'name' => 'Marketing', 'color' => '#000000']);

    $task = Task::create([
        'organization_id' => $org->id,
        'project_id' => $project->id,
        'department_id' => $dept->id,
        'title' => 'Pre-migration-style task',
        'priority' => 'medium',
        'status' => 'pending',
    ]);

    // No storage_key/sha256_hash/size_bytes/mime_type/original_filename/
    // origin_task_id given — exactly what a row created before this phase
    // looks like; the migration adds no backfill for any of them.
    $document = Document::create([
        'organization_id' => $org->id,
        'uploaded_by' => $uploader->id,
        'name' => 'Pre-migration doc',
        'link' => 'https://example.com/pre-migration.pdf',
        'access_level' => 'internal',
    ]);
    $task->documents()->attach($document->id);

    $document = $document->fresh();
    expect($document->name)->toBe('Pre-migration doc');
    expect($document->link)->toBe('https://example.com/pre-migration.pdf');
    expect($document->storage_key)->toBeNull();
    // Document::url() falls back to `link` whenever storage_key is null —
    // the one helper every consumer now routes through (task #73 phase 1
    // requirement 2).
    expect($document->url)->toBe('https://example.com/pre-migration.pdf');
    expect($task->fresh()->documents()->pluck('documents.id')->all())->toBe([$document->id]);
});

test('the client-view-documents cleanup migration detaches an existing grant but leaves other Client permissions alone', function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $clientRole = Role::where('slug', 'client')->firstOrFail();
    $viewDocuments = Permission::where('slug', 'view_documents')->firstOrFail();
    $manageDocuments = Permission::where('slug', 'manage_documents')->firstOrFail();

    // Simulate a live install where an owner manually granted it before
    // this phase locked it off in the UI.
    $clientRole->permissions()->syncWithoutDetaching([$viewDocuments->id, $manageDocuments->id]);
    expect($clientRole->fresh()->permissions()->pluck('slug')->all())->toContain('view_documents', 'manage_documents');

    removeClientViewDocumentsMigration()->up();

    $freshSlugs = $clientRole->fresh()->permissions()->pluck('slug')->all();
    expect($freshSlugs)->not->toContain('view_documents');
    expect($freshSlugs)->toContain('manage_documents');
});

test('the client-view-documents cleanup migration is a no-op when Client never held it', function () {
    $this->seed([RoleSeeder::class, PermissionSeeder::class]);

    $clientRole = Role::where('slug', 'client')->firstOrFail();
    $before = $clientRole->permissions()->pluck('slug')->sort()->values()->all();

    removeClientViewDocumentsMigration()->up();

    expect($clientRole->fresh()->permissions()->pluck('slug')->sort()->values()->all())->toBe($before);
});
