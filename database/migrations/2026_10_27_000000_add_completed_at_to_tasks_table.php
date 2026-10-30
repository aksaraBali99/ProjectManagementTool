<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * task #72 phase 6: when a task was last completed. Set by
 * TaskObserver::saving() from now on; existing completed tasks are filled
 * in by `php artisan tasks:backfill-completed-at`, run separately after
 * this migration so it can be dry-run and verified on its own.
 *
 * The index serves the Kanban Completed column (completed_at DESC, id DESC
 * read straight from the index, no sort) and phase 7's "completed in the
 * last N days" range — EXPLAIN on the 2,000-task perf data showed MySQL
 * choosing it for both.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('start_date');
            $table->index(['organization_id', 'status', 'completed_at'], 'tasks_org_status_completed_at_index');
        });
    }

    public function down(): void
    {
        // The organization_id foreign key keeps its index after this one
        // goes: phase 3's indexes also start with organization_id.
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_org_status_completed_at_index');
            $table->dropColumn('completed_at');
        });
    }
};
