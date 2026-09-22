<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TEXT (64KB) is too small once description/body hold rich-text HTML
     * with embedded media references (later phases of task #4) — widened to
     * LONGTEXT now so the editor swap and the media phases don't need a
     * schema change mid-stream.
     *
     * Widening only: each column keeps its existing nullability
     * (tasks.description nullable, comments.body NOT NULL) and no value is
     * read, rewritten, or converted. Existing plain-text rows stay exactly
     * as stored; they're only reinterpreted at render/edit time by
     * App\Support\RichText, never rewritten by this migration.
     *
     * SQLite is skipped on purpose. Its TEXT has no 64KB ceiling (there is
     * nothing to widen), and Laravel implements change() there by rebuilding
     * the whole table — dropping the old `tasks` table fires the
     * ON DELETE CASCADE foreign keys from comments/subtasks/task_documents
     * and silently deletes their rows. On MySQL (dev/production) change() is
     * an in-place ALTER TABLE ... MODIFY with no such side effect.
     * LongtextMigrationTest guards this: it fails if child rows are lost.
     */
    public function up(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->longText('description')->nullable()->change();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->longText('body')->nullable(false)->change();
        });
    }

    /**
     * Narrowing back to TEXT will fail (or truncate, depending on the SQL
     * mode) if any row now holds more than 64KB — rolling back after rich
     * content has been saved is not lossless, which is inherent to going
     * from LONGTEXT to TEXT rather than something this migration can hide.
     */
    public function down(): void
    {
        if ($this->isSqlite()) {
            return;
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->text('body')->nullable(false)->change();
        });
    }

    private function isSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }
};
