<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Task #4, clipboard paste — one row per clipboard-pasted image/video
 * successfully stored against an already-existing task. This is purely a
 * count-and-lock ledger for PastedMediaNamer's atomic {task-title-slug}-
 * {image|video}-{n} numbering: a picker-selected upload never creates a
 * row here, and nothing in the UI lists or links to a row directly the
 * way it would a Document. Tenant-owned (organization_id +
 * BelongsToOrganization) like every other per-task table, even though
 * every real query also filters by task_id — the same defense-in-depth
 * the rest of this schema already applies uniformly.
 *
 * A row is written only on a successful store, never pre-incremented and
 * then rolled back on a failed upload — so the count PastedMediaNamer
 * reads is always exactly "how many pasted files of this type actually
 * exist for this task right now", not a separate counter that could
 * drift from reality after a failure.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasted_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->enum('file_type', ['image', 'video']);
            $table->string('filename');
            $table->timestamps();

            $table->index(['task_id', 'file_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasted_media');
    }
};
