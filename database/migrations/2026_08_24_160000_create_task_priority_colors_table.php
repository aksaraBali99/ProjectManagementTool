<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Same reasoning as task_status_colors: seeds the current hardcoded
     * Priority::badgeBackground()/badgeText() values directly here, not a
     * separate seeder, since every environment needs a row for every
     * priority the moment this table exists.
     */
    public function up(): void
    {
        Schema::create('task_priority_colors', function (Blueprint $table) {
            $table->id();
            $table->string('priority')->unique();
            $table->string('background_color');
            $table->string('text_color');
            $table->timestamps();
        });

        $now = now();

        DB::table('task_priority_colors')->insert([
            ['priority' => 'high', 'background_color' => '#FDEAEA', 'text_color' => '#A32D2D', 'created_at' => $now, 'updated_at' => $now],
            ['priority' => 'medium', 'background_color' => '#FEF5E7', 'text_color' => '#854F0B', 'created_at' => $now, 'updated_at' => $now],
            ['priority' => 'low', 'background_color' => '#FEFCE6', 'text_color' => '#706910', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('task_priority_colors');
    }
};
