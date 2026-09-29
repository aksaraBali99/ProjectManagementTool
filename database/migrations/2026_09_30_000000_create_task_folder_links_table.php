<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * task #73 phase 4: attaching a whole folder to a task, alongside the
     * existing task_documents (single-file) links. Deliberately its own
     * table, not a variant row shape inside task_documents — a folder link
     * has no document_id at all, and mixing "this row is a file" / "this
     * row is a folder" into one table would need a discriminator column
     * and nullable FKs either way.
     *
     * Both task_id and folder_id are restrict-on-delete, NEVER cascade —
     * unlike task_documents (which cascades: deleting a task should just
     * quietly drop its file links, the file survives regardless). Here,
     * deleting the task or the folder while linked must go through the
     * app's own unlink-first flow instead, the same restrict-and-enforce-
     * in-the-app pattern document_folders/documents.folder_id already use
     * (see that migration's own docblock) — a DB-level backstop, not the
     * primary enforcement (that's TaskFolderController::detach() /
     * DocumentFolderController::destroy()'s own linked-count check).
     */
    public function up(): void
    {
        Schema::create('task_folder_links', function (Blueprint $table) {
            $table->foreignId('task_id')->constrained()->restrictOnDelete();
            $table->foreignId('folder_id')->constrained('document_folders')->restrictOnDelete();
            $table->foreignId('linked_by')->constrained('users');
            $table->timestamps();

            $table->unique(['task_id', 'folder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_folder_links');
    }
};
