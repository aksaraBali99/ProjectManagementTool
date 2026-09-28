<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * task #73 phase 1: view_documents is now permanently locked off for
     * Client in the Role Permissions screen (see PermissionManagementController
     * ::FORCED_OFF) — PermissionSeeder already never grants it to Client by
     * default, but a live install could have an owner who manually toggled
     * it on before this phase shipped, back when the matrix let them.
     * Defensive cleanup for that case, not a correction to the seeder
     * itself.
     */
    public function up(): void
    {
        $client = Role::where('slug', Role::CLIENT)->first();
        $viewDocuments = Permission::where('slug', 'view_documents')->first();

        if ($client !== null && $viewDocuments !== null) {
            $client->permissions()->detach($viewDocuments->id);
        }
    }

    public function down(): void
    {
        // Deliberately irreversible: re-granting view_documents to Client
        // would contradict the whole point of the forced-off lock this
        // phase introduces, and nothing downstream depends on restoring
        // whatever pre-migration grant (if any) this removed.
    }
};
