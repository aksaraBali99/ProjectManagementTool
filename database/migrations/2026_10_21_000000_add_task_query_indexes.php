<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * task #72 phase 3: indexes for the per-company task queries phases 4, 5
 * and 10 will run. Each one was kept only because EXPLAIN on the 2,000-task
 * perf data showed MySQL actually choosing it:
 *
 * - (organization_id, due_date): the Tasks list page in due-date order
 *   (LIMIT 25 reads 25 index entries instead of sorting the company) and
 *   the Calendar's month range.
 * - (organization_id, status): one Kanban column, and its column counts.
 * - (organization_id, updated_at): "tasks changed since X" polling.
 *
 * Indexes only: no column or row is touched.
 */
return new class extends Migration
{
    private const INDEXES = [
        'tasks_org_due_date_index' => ['organization_id', 'due_date'],
        'tasks_org_status_index' => ['organization_id', 'status'],
        'tasks_org_updated_at_index' => ['organization_id', 'updated_at'],
    ];

    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            foreach (self::INDEXES as $name => $columns) {
                $table->index($columns, $name);
            }
        });
    }

    public function down(): void
    {
        // MySQL silently drops the index it created implicitly for the
        // organization_id foreign key once another index starting with
        // organization_id exists (verified on MySQL 8.4 with this exact
        // migration). Dropping our indexes would then leave the FK with no
        // index and fail with "needed in a foreign key constraint", so put
        // the single-column one back first, under its original name.
        // SQLite never indexes foreign keys, so there is nothing to restore.
        $needsFkIndex = in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)
            && ! collect(Schema::getIndexes('tasks'))->contains(
                fn (array $index) => ($index['columns'][0] ?? null) === 'organization_id'
                    && ! array_key_exists($index['name'], self::INDEXES)
            );

        Schema::table('tasks', function (Blueprint $table) use ($needsFkIndex) {
            if ($needsFkIndex) {
                $table->index('organization_id', 'tasks_organization_id_foreign');
            }

            foreach (array_keys(self::INDEXES) as $name) {
                $table->dropIndex($name);
            }
        });
    }
};
