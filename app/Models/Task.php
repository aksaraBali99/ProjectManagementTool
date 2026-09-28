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
use Illuminate\Support\Collection;

#[Fillable(['organization_id', 'project_id', 'department_id', 'assignee_id', 'title', 'description', 'priority', 'status', 'due_date', 'start_date'])]
#[ObservedBy(TaskObserver::class)]
class Task extends Model
{
    use BelongsToOrganization, SoftDeletes;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'start_date' => 'date',
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

    /**
     * task #73 phase 2: batched TaskPolicy::view(), for a viewer checking
     * many tasks at once (the Documents page's origin column, the edit/
     * delete dialogs' linked-task lists) without one Gate::allows() query
     * set per task. Mirrors TaskPolicy::view() branch-for-branch — see
     * TaskViewableIdsParityTest, which asserts the two never disagree
     * across a matrix of roles/states — so a caller can use whichever is
     * more convenient without risking a different answer.
     *
     * Deliberately NOT built on scopeVisibleTo(): that scope disagrees
     * with TaskPolicy::view() in two ways (no hasPermission('view_tasks')
     * gate, and allowedDepartmentIds() doesn't filter out a deactivated
     * department) — a decision to leave alone this phase, not something
     * this helper should inherit.
     *
     * One query per branch per organization represented in $taskIds, not
     * one per task — grouping by organization_id first is what makes that
     * possible, since hasPermission()/isManagementInOrg()/isClientInOrg()
     * are all themselves org-scoped checks.
     *
     * @param  array<int, int>  $taskIds
     * @return Collection<int, int> the subset of $taskIds this user can view
     */
    public static function viewableIdsFor(User $user, array $taskIds): Collection
    {
        $taskIds = array_values(array_unique($taskIds));

        if (empty($taskIds)) {
            return collect();
        }

        $tasks = static::query()->whereIn('id', $taskIds)
            ->get(['id', 'organization_id', 'department_id', 'assignee_id', 'project_id']);

        $viewableIds = collect();

        foreach ($tasks->groupBy('organization_id') as $organizationId => $orgTasks) {
            $organizationId = (int) $organizationId;

            if (! $user->hasPermission('view_tasks', $organizationId)) {
                continue;
            }

            if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organizationId)) {
                $viewableIds->push(...$orgTasks->pluck('id'));

                continue;
            }

            // Active-department access — one query for every department
            // this batch of tasks touches in this org, mirroring
            // User::hasDepartmentAccess()'s own is_active filter exactly.
            $allowedDepartmentIds = $user->accessPermissions()
                ->where('organization_id', $organizationId)
                ->where('allowed', true)
                ->whereHas('department', fn ($query) => $query->where('is_active', true))
                ->pluck('department_id');

            // Client-of-project — one query, only if this user actually
            // holds the Client role in this org at all.
            $clientProjectIds = $user->isClientInOrg($organizationId)
                ? $user->projectsAsClient()->where('organization_id', $organizationId)->pluck('projects.id')
                : collect();

            // Subtask-assignee — one grouped query for every task in this
            // org, not one per task.
            $subtaskAssigneeTaskIds = Subtask::whereIn('task_id', $orgTasks->pluck('id'))
                ->where('assignee_id', $user->id)
                ->pluck('task_id');

            foreach ($orgTasks as $task) {
                if ($allowedDepartmentIds->contains($task->department_id)
                    || $task->assignee_id === $user->id
                    || $subtaskAssigneeTaskIds->contains($task->id)
                    || $clientProjectIds->contains($task->project_id)) {
                    $viewableIds->push($task->id);
                }
            }
        }

        return $viewableIds->values();
    }

    /**
     * Every user who would actually pass TaskPolicy::view() for THIS task
     * — the reverse direction of that check (given a task, who can see
     * it, rather than given a user, can they see this task). Not scoped
     * to mentions specifically: any UI needing "who can actually open
     * this task" should reuse this rather than a project-attachment
     * proxy like ValidatesTaskAssignment::isAssignableStaffForProject(),
     * which answers a related but different question (assignable to the
     * PROJECT) and can disagree with real task-view permission across
     * departments — that mismatch was a genuine permission leak in the
     * comment @mention autocomplete, which this method was built to fix.
     *
     * Built by gathering every user reachable through view()'s disjoint
     * OR-branches (global role, management in this org, department
     * access to this task's own department, the assignee, subtask
     * assignees, the project's client) as candidates, then filtering
     * that — deliberately small, per-task — set through the exact same
     * hasPermission('view_tasks', ...) gate view() itself requires before
     * any of those branches count. Reuses the real method rather than
     * re-deriving the permission check in SQL, so this can never drift
     * from the single-user policy it mirrors.
     *
     * @return Collection<int, User>
     */
    public function viewableUsers(): Collection
    {
        $organizationId = $this->organization_id;

        $candidateIds = collect();

        // Global roles (super_admin/owner) — unconditional in view() once
        // past the view_tasks gate, which they always hold.
        $candidateIds->push(...User::withGlobalRole()->pluck('id'));

        // Management in this org.
        $candidateIds->push(...User::whereHas(
            'orgMemberships',
            fn ($query) => $query->where('organization_id', $organizationId)
                ->whereHas('role', fn ($roleQuery) => $roleQuery->where('slug', Role::MANAGEMENT))
        )->pluck('id'));

        // Department access to THIS task's specific department — mirrors
        // hasDepartmentAccess() exactly (including its is_active
        // department check), not "any staff in the org".
        $candidateIds->push(...AccessPermission::where('organization_id', $organizationId)
            ->where('department_id', $this->department_id)
            ->where('allowed', true)
            ->whereHas('department', fn ($query) => $query->where('is_active', true))
            ->pluck('user_id'));

        // The assignee.
        if ($this->assignee_id !== null) {
            $candidateIds->push($this->assignee_id);
        }

        // Subtask assignees.
        $candidateIds->push(...$this->subtasks()->whereNotNull('assignee_id')->pluck('assignee_id'));

        // The project's client(s).
        $candidateIds->push(...$this->project->clients()->pluck('users.id'));

        $candidateIds = $candidateIds->map(fn ($id) => (int) $id)->unique()->values();

        if ($candidateIds->isEmpty()) {
            return collect();
        }

        // The one gate every branch above still needs, applied via the
        // real method rather than re-derived here — see docblock.
        return User::whereIn('id', $candidateIds)
            ->get()
            ->filter(fn (User $user) => $user->hasPermission('view_tasks', $organizationId))
            ->values();
    }
}
