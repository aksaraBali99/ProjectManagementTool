<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->boolean('allowed')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'organization_id', 'department_id'], 'access_permissions_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_permissions');
    }
};
