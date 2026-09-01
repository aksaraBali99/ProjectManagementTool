<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A genuinely system-triggered audit entry (e.g. solva:bootstrap
     * --reset-owner, run over SSH with no authenticated web session and
     * no natural "current organization") has no truthful non-null value
     * for either column — every other write site keeps passing real
     * values, this just stops forcing an artificial one where none
     * exists. The Audit Trail view already null-guards both
     * ($entry->user->name ?? 'Unknown user', @if ($entry->organization)),
     * so this doesn't require any view-layer change.
     */
    public function up(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('organization_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('audit_log', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable(false)->change();
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
