<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['organization_id', 'name', 'description', 'is_external', 'status', 'priority'])]
class Project extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'is_external' => 'boolean',
            'status' => ProjectStatus::class,
            'priority' => Priority::class,
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

    /**
     * The project's single client, or null for an Internal project. The
     * `clients` pivot supports many rows, but this form only ever writes
     * zero or one.
     */
    public function primaryClient(): ?User
    {
        return $this->clients->first();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
