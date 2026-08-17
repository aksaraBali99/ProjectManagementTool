<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['organization_id', 'name', 'description', 'client_name', 'is_external', 'status', 'priority'])]
class Project extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'is_external' => 'boolean',
        ];
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_staff');
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_clients');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
