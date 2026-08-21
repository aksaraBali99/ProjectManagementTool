<?php

namespace App\Policies;

use App\Models\User;

/**
 * viewAny only — deliberately no update()/delete() methods, and no
 * corresponding routes/controller actions exist anywhere in the app.
 * Audit entries are never editable or deletable, by any role including
 * super_admin/owner: the safest way to guarantee that is to never build
 * the capability at all, not to rely on a policy check someone could get
 * wrong later.
 */
class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_settings');
    }
}
