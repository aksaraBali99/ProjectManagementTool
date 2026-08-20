<?php

namespace App\Http\Requests\Users\Concerns;

use App\Models\Organization;
use App\Models\Role;
use Illuminate\Validation\Validator;

/**
 * Shared business-rule checks for the per-company `roles` input, used by
 * both StoreUserRequest and UpdateUserRequest so the two forms can't drift
 * out of sync.
 */
trait ValidatesCompanyRoles
{
    /**
     * @param  array<int|string, string>  $roles  organization_id => 'none' or a company-assignable role slug
     */
    protected function validateCompanyRoles(Validator $validator, array $roles, bool $grantingSuperAdmin): void
    {
        $validOrgIds = Organization::query()->pluck('id')->map(fn ($id) => (string) $id)->all();
        foreach (array_keys($roles) as $organizationId) {
            if (! in_array((string) $organizationId, $validOrgIds, true)) {
                $validator->errors()->add('roles', 'One of the selected companies is invalid.');

                return;
            }
        }

        $clientOrgIds = collect($roles)->filter(fn ($role) => $role === Role::CLIENT)->keys();

        if ($clientOrgIds->count() > 1) {
            $validator->errors()->add('roles', 'A user can only be assigned the Client role in one company.');

            return;
        }

        if ($clientOrgIds->isNotEmpty() && collect($roles)->except($clientOrgIds)->contains(fn ($role) => $role !== 'none')) {
            $validator->errors()->add('roles', 'A user assigned the Client role in one company cannot hold another role in a different company.');

            return;
        }

        if (! $grantingSuperAdmin && ! collect($roles)->contains(fn ($role) => $role !== 'none')) {
            $validator->errors()->add('roles', 'Assign this user a role in at least one company.');
        }
    }

    /**
     * Granting Super Admin is only offered to users who hold Management in
     * at least one company — reserving the promotion path for managers
     * rather than jumping straight from staff/client. A target who already
     * holds Super Admin is exempt, so an unrelated edit (or a plain
     * uncheck-to-revoke) never gets blocked just because their Management
     * role was since removed.
     *
     * @param  array<int|string, string>  $roles  organization_id => 'none' or a company-assignable role slug
     */
    protected function validateSuperAdminGrant(Validator $validator, array $roles, bool $grantingSuperAdmin, bool $targetAlreadySuperAdmin): void
    {
        if (! $grantingSuperAdmin || $targetAlreadySuperAdmin) {
            return;
        }

        if (! collect($roles)->contains(fn ($role) => $role === Role::MANAGEMENT)) {
            $validator->errors()->add('grant_super_admin', 'Grant Super Admin requires assigning this user the Management role in at least one company.');
        }
    }
}
