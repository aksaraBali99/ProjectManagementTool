<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // (project_id, title) is the exact pair the Bulk Import
            // feature's title-matching queries filter by — only the
            // FK-implied index on project_id alone existed before this.
            $table->index(['project_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'title']);
        });
    }
};
