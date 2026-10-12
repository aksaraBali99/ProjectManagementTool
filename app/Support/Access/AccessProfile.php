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
 *
 * Deliberately UNFILTERED and keyed by subject only. It describes what one
 * user holds, never what some other signed-in user is allowed to be told
 * about them. The two helpers whose pre-cache queries did depend on the
 * viewer — the department ones (AccessPermission's BelongsToOrganization)
 * and isClientOnProject() (Project's) — keep that dependence in User,
 * applied to this profile's answers at answer time. Baking the viewer into
 * the profile would make it wrong the moment it were reused for a
 * different viewer, which is exactly what caching by user id invites.
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
        /**
         * Loaded lazily — most requests never ask a client-on-project
         * question. Project id => that project's organization id, because
         * isClientOnProject()'s viewer filter needs the company.
         *
         * @var array<int, int>|null
         */
        private ?array $clientProjects = null,
        /**
         * Loaded lazily — see visibleOrganizationIds().
         *
         * @var array<int, int>|null
         */
        private ?array $visibleOrganizationIds = null,
    ) {}

    /**
     * Four queries up front, regardless of how many companies, roles or
     * departments the user has. Two more are spent only if something asks
     * a client-on-project question (clientProjects()) or needs the user's
     * visible companies (visibleOrganizationIds()) — both lazy, both at
     * most once per request per user.
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
        return array_key_exists($projectId, $this->clientProjects());
    }

    /**
     * The company owning a project this user is a client on, or null if
     * they aren't a client on it. User::isClientOnProject() needs the
     * company to apply the viewer's visible-org filter, which is what
     * Project's BelongsToOrganization scope used to do to this query.
     */
    public function clientProjectOrganizationId(int $projectId): ?int
    {
        return $this->clientProjects()[$projectId] ?? null;
    }

    /** @return array<int, int> */
    public function clientProjectIds(): array
    {
        return array_keys($this->clientProjects());
    }

    /**
     * Project id => organization id, for every project this user is a
     * listed client on.
     *
     * @return array<int, int>
     */
    private function clientProjects(): array
    {
        // Deferred: most requests never ask, so most requests never pay.
        if ($this->clientProjects === null) {
            // INNER JOIN matches the old projectsAsClient() relation, which
            // joined projects too — a pivot row for a deleted project was
            // never counted and still isn't.
            $rows = DB::table('project_clients')
                ->join('projects', 'projects.id', '=', 'project_clients.project_id')
                ->where('project_clients.user_id', $this->userId)
                ->get(['project_clients.project_id', 'projects.organization_id']);

            $this->clientProjects = [];
            foreach ($rows as $row) {
                $this->clientProjects[(int) $row->project_id] = (int) $row->organization_id;
            }
        }

        return $this->clientProjects;
    }

    /**
     * Mirrors User::visibleOrganizationIds(): global-role users see every
     * company, active or not; everyone else only the active companies they
     * hold an org_members row in.
     *
     * Cached here for two reasons. It is what every BelongsToOrganization
     * global scope calls on every scoped query, so it was itself a large
     * part of the per-row cost this phase removes; and it is now also the
     * viewer filter User's department/client helpers apply, which would
     * otherwise put a query back on exactly the per-row path the cache
     * exists to clear.
     *
     * @return array<int, int>
     */
    public function visibleOrganizationIds(): array
    {
        if ($this->visibleOrganizationIds !== null) {
            return $this->visibleOrganizationIds;
        }

        if ($this->hasGlobalRole()) {
            return $this->visibleOrganizationIds = DB::table('organizations')
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return $this->visibleOrganizationIds = DB::table('org_members')
            ->join('organizations', 'organizations.id', '=', 'org_members.organization_id')
            ->where('org_members.user_id', $this->userId)
            ->where('organizations.is_active', true)
            ->pluck('org_members.organization_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
