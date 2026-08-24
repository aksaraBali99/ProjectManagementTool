<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Seeds the current hardcoded TaskStatus::badgeBackground()/badgeText()
     * values directly in the migration, not a separate seeder — every
     * environment (including the ephemeral per-test SQLite DB, which only
     * ever runs migrations, never seeders) needs a row for every status the
     * moment this table exists, since TaskStatus's badge methods now read
     * from it unconditionally.
     */
    public function up(): void
    {
        Schema::create('task_status_colors', function (Blueprint $table) {
            $table->id();
            $table->string('status')->unique();
            $table->string('background_color');
            $table->string('text_color');
            $table->timestamps();
        });

        $now = now();

        DB::table('task_status_colors')->insert([
            ['status' => 'pending', 'background_color' => '#F1EFE8', 'text_color' => '#5F5E5A', 'created_at' => $now, 'updated_at' => $now],
            ['status' => 'in_progress', 'background_color' => '#E1F5EE', 'text_color' => '#0F6E56', 'created_at' => $now, 'updated_at' => $now],
            ['status' => 'in_review', 'background_color' => '#FAEEDA', 'text_color' => '#854F0B', 'created_at' => $now, 'updated_at' => $now],
            ['status' => 'completed', 'background_color' => '#EAF3DE', 'text_color' => '#3B6D11', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('task_status_colors');
    }
};
