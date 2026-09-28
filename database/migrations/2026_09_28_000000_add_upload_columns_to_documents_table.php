<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * task #73 phase 1: documents can now be genuine file uploads, not
     * just external links. storage_key is the only new field an uploaded
     * file's URL is ever built from (see Document::url()) — sha256_hash,
     * size_bytes, mime_type and original_filename are recorded now purely
     * so later work (de-duplication, a file inventory) doesn't need to
     * re-read every file's bytes to backfill them. origin_task_id is
     * provenance only ("where did this get uploaded from"), set once at
     * upload and never changed — null means the Documents page itself.
     *
     * No backfill: every existing row simply gets nulls for all of these
     * and keeps rendering exactly as before (Document::url() falls back
     * to the existing `link` column whenever storage_key is null).
     *
     * $withinTransaction = false, disableForeignKeyConstraints() around
     * the `link` ->change(), AND origin_task_id having no real FK
     * constraint on SQLite all exist for the same underlying reason, and
     * none of them alone is enough — empirically confirmed via a
     * dedicated diagnostic test, since this contradicted the assumption
     * behind an earlier draft of this migration:
     *   - Adding origin_task_id itself as a foreignId()->constrained()
     *     column — no ->change() involved at all — makes Laravel rebuild
     *     the whole `documents` table on SQLite (create/copy/drop/
     *     rename), and dropping the old table mid-rebuild fires ON
     *     DELETE CASCADE for any task_documents row still referencing
     *     it, silently deleting them. Not just a ->change() problem (see
     *     the project's own project_sqlite_change_migration_cascade
     *     note) — any operation the SQLite grammar handles via a table
     *     rebuild carries the same risk.
     *   - Schema::disableForeignKeyConstraints() alone does not stop
     *     this in every context: Laravel's Migrator wraps a migration's
     *     up()/down() in its own transaction by default, and SQLite
     *     silently ignores a `PRAGMA foreign_keys` change made INSIDE an
     *     open transaction — so the toggle is a no-op unless
     *     $withinTransaction = false also takes this migration out of
     *     that wrapping transaction (this matters for a real
     *     `php artisan migrate`/`migrate:rollback` run; it does nothing
     *     for Pest's own ambient per-test transaction, see the next
     *     point).
     *   - Pest's RefreshDatabase wraps every test in its own transaction
     *     too, one this migration has no way to opt out of when a test
     *     calls ->up()/->down() directly (not through the Migrator) to
     *     verify it — so on SQLite specifically, origin_task_id is added
     *     as a plain nullable column with no FK constraint at all,
     *     sidestepping the rebuild risk structurally instead of relying
     *     on a pragma toggle that can't reliably take effect in every
     *     caller. This is an acceptable trade: it's provenance metadata,
     *     not ownership, and tasks are soft-deleted in this app anyway
     *     so the nullOnDelete case this would otherwise enforce never
     *     fires in practice. On MySQL (dev/prod), origin_task_id keeps
     *     its real FK, and every one of these operations is a plain
     *     in-place ALTER with no rebuild at all.
     */
    public $withinTransaction = false;

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('documents', function (Blueprint $table) {
            $table->string('storage_key')->nullable()->after('link');
            $table->string('sha256_hash', 64)->nullable()->after('storage_key');
            $table->unsignedBigInteger('size_bytes')->nullable()->after('sha256_hash');
            $table->string('mime_type')->nullable()->after('size_bytes');
            $table->string('original_filename')->nullable()->after('mime_type');

            if ($this->isSqlite()) {
                $table->unsignedBigInteger('origin_task_id')->nullable()->after('original_filename');
            } else {
                // nullOnDelete, not cascade — this is provenance
                // metadata, not ownership; a document survives even if
                // the task it was originally uploaded from is later gone
                // (tasks are soft-deleted in this app anyway, so this
                // never fires in practice, but it's the correct
                // semantic either way).
                $table->foreignId('origin_task_id')->nullable()->after('original_filename')
                    ->constrained('tasks')->nullOnDelete();
            }
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->string('link')->nullable()->change();
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('documents', function (Blueprint $table) {
            if ($this->isSqlite()) {
                $table->dropColumn('origin_task_id');
            } else {
                $table->dropConstrainedForeignId('origin_task_id');
            }
            $table->dropColumn(['storage_key', 'sha256_hash', 'size_bytes', 'mime_type', 'original_filename']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->string('link')->nullable(false)->change();
        });

        Schema::enableForeignKeyConstraints();
    }

    private function isSqlite(): bool
    {
        return Schema::getConnection()->getDriverName() === 'sqlite';
    }
};
