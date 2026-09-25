<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('emoji');
            $table->timestamp('created_at')->useCurrent();

            // ONE reaction per person per comment — picking a different
            // emoji replaces this row (CommentReactionController::store()),
            // it never inserts a second one. Deliberately not
            // (comment_id, user_id, emoji): that would allow the same
            // person to stack multiple different reactions on one comment,
            // which task #70 phase 4's spec explicitly rules out.
            $table->unique(['comment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
    }
};
