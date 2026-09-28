<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * task #73 phase 2: folders on the Documents page. parent_id is a
     * nullable self-FK (null = a root-level folder for that company) and
     * never changes after creation — moving a folder is out of scope this
     * phase. Both FKs here (and documents.folder_id below) are restrict-
     * on-delete, never cascade: a folder can only ever be removed once
     * it's actually empty (enforced at the application layer in
     * DocumentFolderPolicy/the controller), and the DB constraint is a
     * defense-in-depth backstop against that check ever being bypassed,
     * not the primary enforcement.
     */
    public function up(): void
    {
        Schema::create('document_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('document_folders')->restrictOnDelete();
            $table->string('name');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // No backfill: every existing document simply stays at the root
        // (folder_id null) — this column carries no default other than
        // null, so nothing about how an existing document renders changes.
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('folder_id')->nullable()->after('organization_id')
                ->constrained('document_folders')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('folder_id');
        });

        Schema::dropIfExists('document_folders');
    }
};
