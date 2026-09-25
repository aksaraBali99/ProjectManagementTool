<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One level of threaded replies on Task comments (task #70, phase 2):
 * a reply is just another comments row, self-referencing its top-level
 * parent via parent_comment_id. Nullable — a top-level comment has none.
 * cascadeOnDelete() matches this table's own task_id FK convention:
 * deleting a top-level comment removes its replies too, rather than
 * leaving them pointing at nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('parent_comment_id')->nullable()->after('task_id')
                ->constrained('comments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_comment_id');
        });
    }
};
