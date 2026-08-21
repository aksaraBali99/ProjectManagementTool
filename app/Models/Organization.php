<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'accent_color', 'is_active'])]
class Organization extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Internal, code-facing identifier — never user-editable. Derived from
     * the name at creation time and never regenerated afterward, even if
     * the name changes later, since the slug may already be referenced
     * elsewhere (URLs, cached views) and silently changing it on a rename
     * could break existing links.
     */
    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (self::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'org_members')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function orgMembers(): HasMany
    {
        return $this->hasMany(OrgMember::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function accessPermissions(): HasMany
    {
        return $this->hasMany(AccessPermission::class);
    }

    /**
     * Full pill treatment (background/text) per the UIX spec's Company
     * colour table. Keyed on accent_color, which OrganizationSeeder pins to
     * the spec's dot colour for each of the 3 known companies; any other
     * accent_color (a future 4th+ company) falls back to a neutral pill
     * rather than guessing.
     */
    public function badgeBackground(): string
    {
        return match ($this->accent_color) {
            '#1D9E75' => '#E1F5EE',
            '#534AB7' => '#EEEDFE',
            '#BA7517' => '#FAEEDA',
            default => '#F3F4F6',
        };
    }

    public function badgeText(): string
    {
        return match ($this->accent_color) {
            '#1D9E75' => '#0F6E56',
            '#534AB7' => '#3C3489',
            '#BA7517' => '#854F0B',
            default => '#374151',
        };
    }
}
