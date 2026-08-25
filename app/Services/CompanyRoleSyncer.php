<?php

namespace App\Services;

use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;

/**
 * Extracted from UserManagementController::syncCompanyRoles() so the
 * Import feature's commit stage can reuse the exact same per-key
 * upsert/delete primitive for its Company Roles tab — this only ever
 * touches organization ids present as keys in $roles, which is exactly
 * the "leave untouched pairs alone" semantics both the in-app form and
 * Import need (the in-app form submits every company so it behaves like
 * a full resync; Import submits only what its Company Roles sheet
 * mentions for a given username, on purpose).
 */
class CompanyRoleSyncer
{
    /**
     * @param  array<int|string, string>  $roles  organization_id => 'none' or a company-assignable role slug
     * @return array<int|string, string> organization_id => 'created'|'updated'|'deleted', only for orgs actually touched (a 'none' for an org with no existing row touches nothing)
     */
    public function sync(User $user, array $roles): array
    {
        $roleIds = Role::assignableInCompany()->pluck('id', 'slug');
        $changes = [];

        foreach ($roles as $organizationId => $slug) {
            if ($slug === 'none') {
                $deleted = OrgMember::where('organization_id', $organizationId)
                    ->where('user_id', $user->id)
                    ->delete();

                if ($deleted > 0) {
                    $changes[$organizationId] = 'deleted';
                }

                continue;
            }

            $existing = OrgMember::where('organization_id', $organizationId)->where('user_id', $user->id)->exists();

            OrgMember::updateOrCreate(
                ['organization_id' => $organizationId, 'user_id' => $user->id],
                ['role_id' => $roleIds[$slug]],
            );

            $changes[$organizationId] = $existing ? 'updated' : 'created';
        }

        return $changes;
    }
}
