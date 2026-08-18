<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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

    public function hasDepartmentAccess(int $organizationId, int $departmentId): bool
    {
        return $this->accessPermissions()
            ->where('organization_id', $organizationId)
            ->where('department_id', $departmentId)
            ->where('allowed', true)
            ->whereHas('department', fn ($query) => $query->where('is_active', true))
            ->exists();
    }

    public function isClientOnProject(int $projectId): bool
    {
        return $this->projectsAsClient()->where('projects.id', $projectId)->exists();
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
}
