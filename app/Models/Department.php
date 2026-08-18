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
}
