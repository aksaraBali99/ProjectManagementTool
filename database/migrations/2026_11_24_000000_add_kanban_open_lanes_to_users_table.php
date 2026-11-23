<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SOL-0025 phase 2: which Kanban lanes each user left open, per company:
 * {"<organization_id>": [<project_id>, ...]}. Null (or no key for a
 * company) means nothing open, as on a first visit; no backfill. Additive
 * only, so it is safe whichever of code and migration lands first.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('kanban_open_lanes')->nullable()->after('tasks_per_page');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kanban_open_lanes');
        });
    }
};
