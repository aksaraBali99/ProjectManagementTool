<?php

namespace App\Support\Access;

use App\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 1: everything one user's role/permission/membership/
 * department-access questions need, loaded once and answered from memory.
 *
 * Built with raw DB::table() queries, and that is NOT a style choice —
 * it is required. AccessPermission and Department both use
 * BelongsToOrganization (and Department also HidesInactiveFromNonAdmins),
 * whose global scopes call $user->visibleOrganizationIds() and
 * $user->hasGlobalRole() on every query. Building this profile through
 * Eloquent would therefore re-enter the very helpers it exists to answer,
 * recursing until the stack ran out. Raw queries bypass global scopes
 * entirely, which is also why those scopes' own cost disappears once the
 * helpers they call are cached — a large part of this phase's win.
 *
 * Immutable once built: anything that changes the underlying data flushes
 * the whole profile rather than mutating it (see AccessProfileCache).
 */
final class AccessProfile
{
    /**
     * @param  array<int, string>  $globalRoleSlugs  super_admin / owner
     * @param  array<int, int>  $globalRoleIds
     * @param  array<int, array{role_id: int, slug: string}>  $orgRoles  keyed by organization id
     * @param  array<int, array<int, string>>  $permissionSlugsByRoleId
     * @param  array<int, array<int, bool>>  $departmentGrants  org id => [department id => is_active]
     */
    private function __construct(
        private readonly int $userId,
        private readonly array $globalRoleSlugs,
        private readonly array $globalRoleIds,
        private readonly array $orgRoles,
        private readonly array $permissionSlugsByRoleId,
        private readonly array $departmentGrants,
        /** Loaded lazily — most requests never ask a client-on-project question. */
        private ?array $clientProjectIds = null,
    ) {}

    /**
     * Four queries up front, regardless of how many companies, roles or
     * departments the user has. A fifth is spent only if something asks a
     * client-on-project question (see clientProjectIds()).
     */
    public static function load(int $userId): self
    {
        $globalRoles = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->get(['roles.id', 'roles.slug']);

        $orgMemberships = DB::table('org_members')
            ->join('roles', 'roles.id', '=', 'org_members.role_id')
            ->where('org_members.user_id', $userId)
            ->get(['org_members.organization_id', 'org_members.role_id', 'roles.slug']);

        $orgRoles = [];
        foreach ($orgMemberships as $membership) {
            $orgRoles[(int) $membership->organization_id] = [
                'role_id' => (int) $membership->role_id,
                'slug' => (string) $membership->slug,
            ];
        }

        $heldRoleIds = array_values(array_unique(array_merge(
            $globalRoles->pluck('id')->map(fn ($id) => (int) $id)->all(),
            array_column($orgRoles, 'role_id'),
        )));

        $permissionSlugsByRoleId = [];
        if ($heldRoleIds !== []) {
            $rows = DB::table('role_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->whereIn('role_permissions.role_id', $heldRoleIds)
                ->get(['role_permissions.role_id', 'permissions.slug']);

            foreach ($rows as $row) {
                $permissionSlugsByRoleId[(int) $row->role_id][] = (string) $row->slug;
            }
        }

        // allowed = true only, carrying the department's active flag so
        // allowedDepartmentIds() (active or not) and
        // allowedActiveDepartmentIds()/hasDepartmentAccess() (active only)
        // can both be answered from this one load without conflating them.
        // INNER JOIN is safe: access_permissions.department_id cascades on
        // delete, so a grant can never outlive its department.
        $grants = DB::table('access_permissions')
            ->join('departments', 'departments.id', '=', 'access_permissions.department_id')
            ->where('access_permissions.user_id', $userId)
            ->where('access_permissions.allowed', true)
            ->get(['access_permissions.organization_id', 'access_permissions.department_id', 'departments.is_active']);

        $departmentGrants = [];
        foreach ($grants as $grant) {
            $departmentGrants[(int) $grant->organization_id][(int) $grant->department_id] = (bool) $grant->is_active;
        }

        return new self(
            $userId,
            $globalRoles->pluck('slug')->map(fn ($slug) => (string) $slug)->all(),
            $globalRoles->pluck('id')->map(fn ($id) => (int) $id)->all(),
            $orgRoles,
            $permissionSlugsByRoleId,
            $departmentGrants,
        );
    }

    public function isSuperAdmin(): bool
    {
        return in_array(Role::SUPER_ADMIN, $this->globalRoleSlugs, true);
    }

    public function isOwner(): bool
    {
        return in_array(Role::OWNER, $this->globalRoleSlugs, true);
    }

    public function hasGlobalRole(): bool
    {
        return $this->isSuperAdmin() || $this->isOwner();
    }

    public function hasOrgRole(int $organizationId, string $slug): bool
    {
        return ($this->orgRoles[$organizationId]['slug'] ?? null) === $slug;
    }

    /**
     * Mirrors User::hasPermission() exactly: the permission must be held
     * by one of the user's GLOBAL roles, or — when an organization is
     * given — by their role in that organization. No roles at all means
     * false, never true.
     */
    public function hasPermission(string $slug, ?int $organizationId = null): bool
    {
        $roleIds = $this->globalRoleIds;

        if ($organizationId !== null && isset($this->orgRoles[$organizationId])) {
            $roleIds[] = $this->orgRoles[$organizationId]['role_id'];
        }

        foreach (array_unique($roleIds) as $roleId) {
            if (in_array($slug, $this->permissionSlugsByRoleId[$roleId] ?? [], true)) {
                return true;
            }
        }

        return false;
    }

    /** Granted AND the department still active — matches hasDepartmentAccess(). */
    public function hasDepartmentAccess(int $organizationId, int $departmentId): bool
    {
        return ($this->departmentGrants[$organizationId][$departmentId] ?? false) === true;
    }

    /**
     * Every granted department, active or not.
     *
     * @return Collection<int, int>
     */
    public function allowedDepartmentIds(int $organizationId): Collection
    {
        return Collection::make(array_keys($this->departmentGrants[$organizationId] ?? []));
    }

    /**
     * Granted departments that are still active.
     *
     * @return Collection<int, int>
     */
    public function allowedActiveDepartmentIds(int $organizationId): Collection
    {
        return Collection::make($this->departmentGrants[$organizationId] ?? [])
            ->filter(fn (bool $isActive) => $isActive)
            ->keys();
    }

    /** @return array<int, int> organization ids this user holds any role in */
    public function organizationIdsWithRole(): array
    {
        return array_keys($this->orgRoles);
    }

    public function isClientOnProject(int $projectId): bool
    {
        return in_array($projectId, $this->clientProjectIds(), true);
    }

    /** @return array<int, int> */
    public function clientProjectIds(): array
    {
        // Deferred: most requests never ask, so most requests never pay.
        if ($this->clientProjectIds === null) {
            $this->clientProjectIds = DB::table('project_clients')
                ->where('user_id', $this->userId)
                ->pluck('project_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return $this->clientProjectIds;
    }
}
