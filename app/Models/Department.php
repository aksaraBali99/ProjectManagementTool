<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Models\Concerns\HidesInactiveFromNonAdmins;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['organization_id', 'name', 'color', 'is_active'])]
class Department extends Model
{
    use BelongsToOrganization, HidesInactiveFromNonAdmins;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function accessPermissions(): HasMany
    {
        return $this->hasMany(AccessPermission::class);
    }

    /**
     * Name-keyed per the UIX spec's category colour table (6 of its 8
     * categories match seeded departments; anything else falls back to a
     * neutral badge rather than guessing a colour for a custom department).
     */
    public function badgeBackground(): string
    {
        return match ($this->name) {
            'Marketing' => '#EEEDFE',
            'Operations' => '#E6F1FB',
            'Sales' => '#FAECE7',
            'Training' => '#EAF3DE',
            'Technology' => '#F1EFE8',
            'Biz Dev' => '#FAEEDA',
            default => '#F3F4F6',
        };
    }

    public function badgeText(): string
    {
        return match ($this->name) {
            'Marketing' => '#3C3489',
            'Operations' => '#0C447C',
            'Sales' => '#712B13',
            'Training' => '#3B6D11',
            'Technology' => '#5F5E5A',
            'Biz Dev' => '#854F0B',
            default => '#374151',
        };
    }
}
