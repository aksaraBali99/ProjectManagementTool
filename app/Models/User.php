<?php

namespace App\Models;

use App\Enums\BoardAccessDeniedReason;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

#[Fillable(['username', 'name', 'employee_id', 'email', 'phone', 'password', 'is_active', 'auth_provider', 'provider_id'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function orgMemberships(): HasMany
    {
        return $this->hasMany(OrgMember::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'org_members')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    public function accessPermissions(): HasMany
    {
        return $this->hasMany(AccessPermission::class);
    }

    public function projectsAsStaff(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_staff');
    }

    public function projectsAsClient(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_clients');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function uploadedDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function notificationSettings(): HasMany
    {
        return $this->hasMany(NotificationSetting::class, 'owner_id');
    }

    /**
     * First letter of the first and last words of the name ("Olivia Larsen"
     * -> "OL"), per the UIX spec's avatar examples. A single-word name
     * falls back to just its first letter rather than repeating it, since
     * the spec gives no guidance for that case.
     */
    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name)) ?: [];
        $words = array_values(array_filter($words, fn ($word) => $word !== ''));

        if (count($words) === 0) {
            return '';
        }

        if (count($words) === 1) {
            return mb_strtoupper(mb_substr($words[0], 0, 1));
        }

        return mb_strtoupper(mb_substr($words[0], 0, 1).mb_substr($words[count($words) - 1], 0, 1));
    }

    /**
     * Deterministic hash-based avatar colour (id % 8) into the UIX spec's
     * 8-pair palette, rather than a colour hardcoded per person, so any
     * user gets a stable, spec-matching avatar colour automatically.
     *
     * @return array{0: string, 1: string} [background, text]
     */
    private function avatarPalette(): array
    {
        $palette = [
            ['#9FE1CB', '#085041'],
            ['#CECBF6', '#3C3489'],
            ['#F5C4B3', '#712B13'],
            ['#B5D4F4', '#0C447C'],
            ['#FAC775', '#633806'],
            ['#E1F5EE', '#0F6E56'],
            ['#FDE8F0', '#8B1A4A'],
            ['#FDEAEA', '#A32D2D'],
        ];

        return $palette[$this->id % 8];
    }

    public function avatarBackground(): string
    {
        return $this->avatarPalette()[0];
    }

    public function avatarText(): string
    {
        return $this->avatarPalette()[1];
    }

    public function isSuperAdmin(): bool
    {
        return $this->roles()->where('slug', Role::SUPER_ADMIN)->exists();
    }

    public function isOwner(): bool
    {
        return $this->roles()->where('slug', Role::OWNER)->exists();
    }

    /**
     * True if this user holds super_admin and/or owner — the two global
     * roles that bypass organization scoping entirely. Company-level role
     * assignment doesn't apply to these users.
     */
    public function hasGlobalRole(): bool
    {
        return $this->isSuperAdmin() || $this->isOwner();
    }

    public function isManagementInOrg(int $organizationId): bool
    {
        return $this->orgMemberships()
            ->where('organization_id', $organizationId)
            ->whereHas('role', fn ($query) => $query->where('slug', Role::MANAGEMENT))
            ->exists();
    }

    public function isStaffInOrg(int $organizationId): bool
    {
        return $this->orgMemberships()
            ->where('organization_id', $organizationId)
            ->whereHas('role', fn ($query) => $query->where('slug', Role::STAFF))
            ->exists();
    }

    public function isClientInOrg(int $organizationId): bool
    {
        return $this->orgMemberships()
            ->where('organization_id', $organizationId)
            ->whereHas('role', fn ($query) => $query->where('slug', Role::CLIENT))
            ->exists();
    }

    public function hasDepartmentAccess(int $organizationId, int $departmentId): bool
    {
        return $this->accessPermissions()
            ->where('organization_id', $organizationId)
            ->where('department_id', $departmentId)
            ->where('allowed', true)
            ->whereHas('department', fn ($query) => $query->where('is_active', true))
            ->exists();
    }

    /**
     * Department IDs this user has been explicitly granted access to within
     * $organizationId, via access_permissions — the department-gating half
     * of Task::scopeVisibleTo(), exposed here so other consumers (e.g. the
     * Dashboard's stricter MyTask filter) can reuse the same derivation
     * instead of re-querying access_permissions themselves.
     *
     * @return Collection<int, int>
     */
    public function allowedDepartmentIds(int $organizationId): Collection
    {
        return $this->accessPermissions()
            ->where('organization_id', $organizationId)
            ->where('allowed', true)
            ->pluck('department_id');
    }

    public function isClientOnProject(int $projectId): bool
    {
        return $this->projectsAsClient()->where('projects.id', $projectId)->exists();
    }

    /**
     * Whether ANY role this user holds grants the given permission — their
     * global roles (user_roles, always checked) plus their per-organization
     * role (org_members) if $organizationId is given. This is a capability
     * check only ("can this role-type do this action at all") — it's layered
     * on top of, not a replacement for, the existing per-company/department
     * scoping helpers like isManagementInOrg()/hasDepartmentAccess(), which
     * answer "specifically in THIS company/department".
     */
    public function hasPermission(string $slug, ?int $organizationId = null): bool
    {
        $roleIds = $this->roles()->pluck('roles.id');

        if ($organizationId !== null) {
            $orgRoleId = $this->orgMemberships()->where('organization_id', $organizationId)->value('role_id');
            if ($orgRoleId) {
                $roleIds->push($orgRoleId);
            }
        }

        if ($roleIds->isEmpty()) {
            return false;
        }

        return Permission::where('slug', $slug)
            ->whereHas('roles', fn ($query) => $query->whereIn('roles.id', $roleIds->unique()))
            ->exists();
    }

    /**
     * The organization IDs this user can manage projects/tasks in —
     * super admins and owners can manage any active company; management
     * users are limited to active companies where they hold the
     * management role via org_members; staff are included only where
     * they've been granted create_edit_projects (org-wide) or
     * create_edit_tasks (and hold at least one department grant there) —
     * this is a coarse pre-filter for the create/edit pages, the actual
     * per-action authority is still each policy's create()/update().
     *
     * @return array<int, int>
     */
    public function manageableOrganizationIds(): array
    {
        if ($this->isSuperAdmin() || $this->isOwner()) {
            return Organization::where('is_active', true)->pluck('id')->all();
        }

        $memberships = $this->orgMemberships()
            ->whereHas('organization', fn ($query) => $query->where('is_active', true))
            ->with('role')
            ->get();

        return $memberships
            ->filter(function (OrgMember $membership) {
                if ($membership->role->slug === Role::MANAGEMENT) {
                    return true;
                }

                if ($membership->role->slug !== Role::STAFF) {
                    return false;
                }

                $organizationId = $membership->organization_id;

                if ($this->hasPermission('create_edit_projects', $organizationId)) {
                    return true;
                }

                return $this->hasPermission('create_edit_tasks', $organizationId)
                    && $this->allowedDepartmentIds($organizationId)->isNotEmpty();
            })
            ->pluck('organization_id')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * The organization IDs this user's queries are allowed to see.
     * Super admins and owners see every organization, active or not;
     * everyone else is limited to active organizations they hold an
     * org_members row in — a deactivated organization disappears from
     * their visibility entirely, same as losing membership.
     *
     * @return array<int, int>
     */
    public function visibleOrganizationIds(): array
    {
        if ($this->isSuperAdmin() || $this->isOwner()) {
            return Organization::query()->pluck('id')->all();
        }

        return $this->orgMemberships()
            ->whereHas('organization', fn ($query) => $query->where('is_active', true))
            ->pluck('organization_id')
            ->all();
    }

    /**
     * The organization IDs that get a company tab on the Dashboard/Kanban
     * boards, gated by $permissionSlug ('view_dashboard' or 'view_kanban'
     * — the two are independently toggleable per role via the Role
     * Permissions admin screen, so a role could hold one but not the
     * other). This is the capability check layer only ("can this role see
     * the board at all") — it's layered on top of, not a replacement for,
     * the existing per-company/department/project scoping, same pattern as
     * TaskPolicy: a client still only gets a tab for a company where
     * they're both holding the Client role AND attached to a project (so a
     * tab never shows up empty — matches exactly what
     * Task::scopeVisibleTo()'s client branch requires to return any
     * tasks), and staff/management's tabs remain unaffected — this
     * permission narrows or widens WHICH ROLES get a board at all, not
     * which departments/projects a staff/client sees once they're in.
     *
     * @return array<int, int>
     */
    public function boardOrganizationIds(string $permissionSlug): array
    {
        if ($this->isSuperAdmin() || $this->isOwner()) {
            return Organization::where('is_active', true)->pluck('id')->all();
        }

        return Collection::make($this->visibleOrganizationIds())
            ->filter(function ($organizationId) use ($permissionSlug) {
                if (! $this->hasPermission($permissionSlug, $organizationId)) {
                    return false;
                }

                return $this->isManagementInOrg($organizationId)
                    || $this->isStaffInOrg($organizationId)
                    || ($this->isClientInOrg($organizationId) && $this->projectsAsClient()->where('organization_id', $organizationId)->exists());
            })
            ->values()
            ->all();
    }

    /**
     * The organization IDs that get a company tab on the Documents page.
     * Unlike boardOrganizationIds(), a client's path here is NOT gated by
     * view_documents — a client never holds that permission by design
     * (DocumentPolicy::view() handles their visibility entirely through
     * access_level + task_documents project-linkage), so they still get a
     * tab for a company where they have an attached project, same as
     * Dashboard/Kanban's client tab, even though the permission check that
     * gates management/staff doesn't apply to them at all.
     *
     * @return array<int, int>
     */
    public function documentOrganizationIds(): array
    {
        if ($this->isSuperAdmin() || $this->isOwner()) {
            return Organization::where('is_active', true)->pluck('id')->all();
        }

        return Collection::make($this->visibleOrganizationIds())
            ->filter(fn ($organizationId) => $this->isManagementInOrg($organizationId)
                || $this->hasPermission('view_documents', $organizationId)
                || $this->projectsAsClient()->where('organization_id', $organizationId)->exists())
            ->values()
            ->all();
    }

    /**
     * Why boardOrganizationIds($permissionSlug) came up empty, so the
     * Dashboard/Kanban empty state can say something accurate instead of
     * one generic message for every cause. Checked in priority order: a
     * role that would otherwise qualify but is missing the permission is a
     * more specific (and more actionable) diagnosis than "no project yet",
     * which in turn is more specific than the true fallback (no
     * organization membership at all, or a deactivated company).
     */
    public function boardAccessDeniedReason(string $permissionSlug): BoardAccessDeniedReason
    {
        $qualifiesByRoleSomewhere = false;
        $hasPermissionWhereQualified = false;
        $isClientSomewhere = false;

        foreach ($this->visibleOrganizationIds() as $organizationId) {
            $roleQualifies = $this->isManagementInOrg($organizationId)
                || $this->isStaffInOrg($organizationId)
                || $this->isClientInOrg($organizationId);

            if (! $roleQualifies) {
                continue;
            }

            $qualifiesByRoleSomewhere = true;

            if ($this->hasPermission($permissionSlug, $organizationId)) {
                $hasPermissionWhereQualified = true;
            }

            if ($this->isClientInOrg($organizationId)) {
                $isClientSomewhere = true;
            }
        }

        if ($qualifiesByRoleSomewhere && ! $hasPermissionWhereQualified) {
            return BoardAccessDeniedReason::PermissionDenied;
        }

        if ($isClientSomewhere && ! $this->projectsAsClient()->exists()) {
            return BoardAccessDeniedReason::NoProject;
        }

        return BoardAccessDeniedReason::NoAccess;
    }
}
