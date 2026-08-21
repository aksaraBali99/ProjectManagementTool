<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Concerns\BelongsToOrganization;
use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['organization_id', 'project_id', 'department_id', 'assignee_id', 'title', 'description', 'priority', 'status', 'due_date'])]
#[ObservedBy(TaskObserver::class)]
class Task extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'priority' => Priority::class,
            'status' => TaskStatus::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'task_documents');
    }

    /**
     * Which tasks in $organizationId are visible to $user. Global roles and
     * management see everything; a client sees only their attached
     * projects' tasks; everyone else (staff) is department-gated via
     * access_permissions, with an assignee-anywhere bypass — a task (or one
     * of its subtasks) assigned to them is visible even outside their
     * granted departments, since being the assignee is its own access
     * path, independent of department scope. This is the single source of
     * truth for task visibility — the Task List, Dashboard, and Kanban
     * pages all filter through this scope rather than re-deriving it.
     */
    public function scopeVisibleTo(Builder $query, User $user, int $organizationId): Builder
    {
        $query->where('organization_id', $organizationId);

        if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organizationId)) {
            return $query;
        }

        if ($user->isClientInOrg($organizationId)) {
            $clientProjectIds = $user->projectsAsClient()->where('organization_id', $organizationId)->pluck('projects.id');

            return $query->whereIn('project_id', $clientProjectIds);
        }

        $allowedDepartmentIds = $user->allowedDepartmentIds($organizationId);

        return $query->where(function ($q) use ($allowedDepartmentIds, $user) {
            $q->whereIn('department_id', $allowedDepartmentIds)
                ->orWhere('assignee_id', $user->id)
                ->orWhereHas('subtasks', fn ($sq) => $sq->where('assignee_id', $user->id));
        });
    }
}
