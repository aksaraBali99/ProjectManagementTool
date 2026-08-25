<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('file_name');
            $table->string('stored_path')->nullable();
            $table->string('status')->default('pending_review');
            // Generated at commit time, not upload time — this is the
            // "UUID batch_id" every audit_log row this import produces
            // gets tagged with (as a matching string column, not a
            // bigint FK — see add_import_batch_id_to_audit_log_table).
            $table->uuid('uuid')->nullable()->unique();
            $table->json('completed_stages')->nullable();
            $table->timestamp('committed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
