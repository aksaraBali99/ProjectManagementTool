<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->cascadeOnDelete();
            $table->string('sheet_name');
            $table->unsignedInteger('row_number');
            $table->json('raw_data');
            $table->string('resolved_action');
            $table->string('validation_status');
            $table->text('validation_message')->nullable();
            // Set post-commit — which real record this row became, both
            // for the review/summary screen ("this row became Task #482")
            // and to reconstruct completed_stages if import_batches'
            // own json column and this ever disagree.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['import_batch_id', 'sheet_name', 'row_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_rows');
    }
};
