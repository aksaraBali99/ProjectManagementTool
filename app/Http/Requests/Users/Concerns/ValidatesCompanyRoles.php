<?php

namespace App\Http\Requests\Users\Concerns;

use App\Support\CompanyRoleRules;
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
        if ($message = CompanyRoleRules::validateRoles($roles, $grantingSuperAdmin)) {
            $validator->errors()->add('roles', $message);
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
        if ($message = CompanyRoleRules::validateSuperAdminGrant($roles, $grantingSuperAdmin, $targetAlreadySuperAdmin)) {
            $validator->errors()->add('grant_super_admin', $message);
        }
    }
}
