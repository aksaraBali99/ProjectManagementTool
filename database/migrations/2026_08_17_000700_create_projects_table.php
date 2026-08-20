<?php

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->string('client_name');
            $table->boolean('is_external')->default(false);
            $table->enum('status', ProjectStatus::values())->default(ProjectStatus::Open->value);
            $table->enum('priority', Priority::values())->default(Priority::Medium->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
