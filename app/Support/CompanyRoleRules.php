<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\Role;

/**
 * The business rules for a user's per-company role assignment — extracted
 * from Http\Requests\Users\Concerns\ValidatesCompanyRoles so the Import
 * feature's bulk validation can apply the exact same rules without faking
 * a FormRequest/Validator context. Framework-agnostic: takes plain data,
 * returns a plain error message or null, never touches the request layer.
 */
class CompanyRoleRules
{
    /**
     * @param  array<int|string, string>  $roles  organization_id => 'none' or a company-assignable role slug
     */
    public static function validateRoles(array $roles, bool $grantingSuperAdmin): ?string
    {
        return static::validateOrganizationIdsExist($roles) ?? static::validateRoleCombination($roles, $grantingSuperAdmin);
    }

    /**
     * Only meaningful when array keys are real organization_id values (the
     * in-app form's dropdown only ever offers existing companies) — the
     * Import feature's Company Roles tab can legitimately reference a
     * brand-new company being created in the same file, so it validates
     * company existence itself (allowing that case) and calls
     * validateRoleCombination() directly instead of this.
     *
     * @param  array<int|string, string>  $roles  organization_id => 'none' or a company-assignable role slug
     */
    public static function validateOrganizationIdsExist(array $roles): ?string
    {
        $validOrgIds = Organization::query()->pluck('id')->map(fn ($id) => (string) $id)->all();
        foreach (array_keys($roles) as $organizationId) {
            if (! in_array((string) $organizationId, $validOrgIds, true)) {
                return 'One of the selected companies is invalid.';
            }
        }

        return null;
    }

    /**
     * The actual role-combination business rules — Client-role exclusivity
     * and "at least one role somewhere" — independent of what the array
     * keys represent (a real organization_id for the in-app form, or a
     * normalized company name for Import), since they only ever inspect
     * the role VALUES.
     *
     * @param  array<int|string, string>  $roles  key => 'none' or a company-assignable role slug
     */
    public static function validateRoleCombination(array $roles, bool $grantingSuperAdmin): ?string
    {
        $clientOrgIds = collect($roles)->filter(fn ($role) => $role === Role::CLIENT)->keys();

        if ($clientOrgIds->count() > 1) {
            return 'A user can only be assigned the Client role in one company.';
        }

        if ($clientOrgIds->isNotEmpty() && collect($roles)->except($clientOrgIds)->contains(fn ($role) => $role !== 'none')) {
            return 'A user assigned the Client role in one company cannot hold another role in a different company.';
        }

        if (! $grantingSuperAdmin && ! collect($roles)->contains(fn ($role) => $role !== 'none')) {
            return 'Assign this user a role in at least one company.';
        }

        return null;
    }

    /**
     * @param  array<int|string, string>  $roles  organization_id => 'none' or a company-assignable role slug
     */
    public static function validateSuperAdminGrant(array $roles, bool $grantingSuperAdmin, bool $targetAlreadySuperAdmin): ?string
    {
        if (! $grantingSuperAdmin || $targetAlreadySuperAdmin) {
            return null;
        }

        if (! collect($roles)->contains(fn ($role) => $role === Role::MANAGEMENT)) {
            return 'Grant Super Admin requires assigning this user the Management role in at least one company.';
        }

        return null;
    }
}
