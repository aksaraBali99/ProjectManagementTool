<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->foreignId('assignee_id')->nullable()->after('title')->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable()->after('is_done');
        });
    }

    public function down(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assignee_id');
            $table->dropColumn('due_date');
        });
    }
};
