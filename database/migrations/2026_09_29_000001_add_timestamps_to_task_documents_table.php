<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * task #73: the Documents page's "Linked tasks" popover needs to order the
 * linked tasks it shows by "most recently linked" — task_documents had no
 * timestamp at all before this. Plain nullable columns (no FK, no
 * default expression), so unlike origin_task_id's migration this needs no
 * SQLite-specific workaround: it's a simple ADD COLUMN on both engines.
 *
 * Existing rows get NULL, which both MySQL and SQLite sort after any real
 * timestamp under `ORDER BY created_at DESC` — pre-existing links just
 * fall to the end of the "most recent first" order rather than needing a
 * backfill.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('task_documents', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('task_documents', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
