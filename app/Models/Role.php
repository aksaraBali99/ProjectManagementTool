<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'description', 'is_system'])]
class Role extends Model
{
    public const SUPER_ADMIN = 'super_admin';

    public const OWNER = 'owner';

    public const MANAGEMENT = 'management';

    public const STAFF = 'staff';

    public const CLIENT = 'client';

    /**
     * Super Admin and Owner are global roles, granted only via the
     * user_roles pivot (never org-scoped through org_members) — they must
     * never appear as options in a per-company role dropdown.
     */
    public const GLOBAL_SLUGS = [self::SUPER_ADMIN, self::OWNER];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    /**
     * Roles that can be assigned to a user within a single company
     * (org_members.role_id) — the single source of truth for what a
     * per-company role dropdown should offer.
     */
    public function scopeAssignableInCompany(Builder $query): Builder
    {
        return $query->whereNotIn('slug', self::GLOBAL_SLUGS);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}
