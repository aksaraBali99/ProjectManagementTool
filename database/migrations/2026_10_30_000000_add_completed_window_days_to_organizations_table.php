<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * task #72 phase 7: how many days a completed task stays on the working
 * pages (Kanban, Tasks list, Dashboard, Projects) before it is hidden by
 * default — 7, 14 or 30, set per company by an owner or super admin. A
 * display rule only: nothing is archived, deactivated or changed.
 *
 * The default covers existing companies and every way one is created
 * (the Add Company form, Import, the perf seeder's raw insert).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->unsignedTinyInteger('completed_window_days')->default(14)->after('accent_color');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('completed_window_days');
        });
    }
};
