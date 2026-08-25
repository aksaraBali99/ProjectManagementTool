<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            // A real, directly-filterable column — matching every other
            // Audit Trail filter dimension (organization_id, entity_type,
            // user_id, action, dates) rather than introducing this
            // codebase's first JSON-path query into `changes`. Stores the
            // same UUID string embedded in changes.import_batch_id, not a
            // bigint FK to import_batches.id, so the two values always
            // match exactly.
            $table->string('import_batch_id')->nullable()->after('entity_id');
            $table->index('import_batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            $table->dropIndex(['import_batch_id']);
            $table->dropColumn('import_batch_id');
        });
    }
};
