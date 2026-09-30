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
use Illuminate\Support\Facades\DB;

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

    /**
     * withoutGlobalScope('active'): a task's department relation must
     * resolve regardless of whether that department is CURRENTLY active —
     * Department::HidesInactiveFromNonAdmins (meant for LISTS/dropdowns a
     * non-admin picks a department from) would otherwise make this
     * relation silently resolve to null for a non-admin the moment the
     * department is deactivated, even though the foreign key is still
     * perfectly valid and the task itself can still be legitimately
     * visible (e.g. via scopeVisibleTo()'s own assignee-anywhere bypass).
     * Every view that renders $task->department->badgeText()/name
     * (Dashboard, Task List, the Projects page's drilldown) would then
     * fatal-error on a null relation instead of just showing a task whose
     * department happens to be deactivated — a real, previously-latent
     * crash, not merely a display nicety.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class)->withoutGlobalScope('active');
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
        // withTimestamps(): see Document::tasks()'s own docblock.
        return $this->belongsToMany(Document::class, 'task_documents')->withTimestamps();
    }

    /**
     * task #73 phase 4: folders attached whole, alongside individual files
     * (documents() above) — task_folder_links, not task_documents.
     * withPivot('linked_by') exposes who linked it via $folder->pivot;
     * TaskDocumentLinker::attachFolder() is what actually writes it, this
     * is just the read side.
     */
    public function folders(): BelongsToMany
    {
        return $this->belongsToMany(DocumentFolder::class, 'task_folder_links', 'task_id', 'folder_id')
            ->withPivot('linked_by')
            ->withTimestamps();
    }

    /**
     * Which tasks in $organizationId are visible to $user. Global roles and
     * management see everything; a client sees only their attached
     * projects' tasks; everyone else (staff) is department-gated via
     * access_permissions, with an assignee-anywhere bypass — a task (or one
     * of its subtasks) assigned to them is visible even outside their
     * granted departments, since being the assignee is its own access
     * path, independent of department scope. This is the single source of
     * truth for task visibility — the Task List, Dashboard, Kanban, and
     * Calendar pages, plus the Projects page's own count/drilldown, all
     * filter through this scope rather than re-deriving it.
     *
     * task #73 (visibility consolidation): now mirrors TaskPolicy::view()
     * in the two ways it used to disagree — the view_tasks permission gate
     * up front (a user with that permission revoked for this org sees no
     * tasks at all, not just a department-filtered subset), and excluding
     * a deactivated department from the department-access branch (a
     * staff member's access_permissions row can still say "allowed" for a
     * department an owner has since deactivated; that grant no longer
     * counts, same as hasDepartmentAccess() already enforces for the
     * single-task check). See TaskViewableIdsParityTest for the batched
     * equivalent's own, already-correct version of this same logic.
     */
    public function scopeVisibleTo(Builder $query, User $user, int $organizationId): Builder
    {
        $query->where('organization_id', $organizationId);

        if (! $user->hasPermission('view_tasks', $organizationId)) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organizationId)) {
            return $query;
        }

        if ($user->isClientInOrg($organizationId)) {
            $clientProjectIds = $user->projectsAsClient()->where('organization_id', $organizationId)->pluck('projects.id');

            return $query->whereIn('project_id', $clientProjectIds);
        }

        // Active-department access only — mirrors hasDepartmentAccess()'s
        // own is_active filter, unlike the bare allowedDepartmentIds()
        // this used to call, which never excluded a deactivated department.
        $allowedDepartmentIds = $user->accessPermissions()
            ->where('organization_id', $organizationId)
            ->where('allowed', true)
            ->whereHas('department', fn ($departmentQuery) => $departmentQuery->where('is_active', true))
            ->pluck('department_id');

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
     * Deliberately NOT built on scopeVisibleTo(): at the time this was
     * written, that scope disagreed with TaskPolicy::view() in two ways
     * (no hasPermission('view_tasks') gate, and allowedDepartmentIds()
     * didn't filter out a deactivated department) — a decision to leave
     * alone that phase, not something this helper should inherit. Both
     * gaps were later fixed directly in scopeVisibleTo() itself (task #73,
     * visibility consolidation), so the two no longer disagree — this
     * method just wasn't refactored to depend on that scope after the
     * fact, since it already independently agreed with TaskPolicy::view()
     * and had its own passing parity test.
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
     * task #70 (unified eligibility): the ONE definition of "who's
     * eligible for this task" — the Assignee dropdown (task and subtask),
     * the @mention autocomplete's base set (see viewableUsers() below),
     * and ValidatesTaskAssignment::isAssignableStaffForProject() (the
     * actual server-side acceptance check) all delegate to this single
     * method now, replacing what used to be two independently-evolving
     * rules (BuildsAssigneeOptions::staffOptionsByProject()'s pure
     * project-membership check, and this same method's own former
     * department-access-alone check) that disagreed in both directions.
     *
     * A user is eligible if ANY of:
     *   1. super_admin or owner (global roles) — always.
     *   2. management in $organizationId — always, no department
     *      restriction (an explicit exception, not an oversight — matches
     *      how management already works everywhere else in this app).
     *   3. this project's client.
     *   4. staff holding BOTH project_staff membership on $projectId AND
     *      active department access (mirrors hasDepartmentAccess()'s own
     *      is_active check) to $departmentId — neither alone is enough
     *      under this rule, unlike either of the two rules it replaces.
     *
     * Deliberately does NOT gate on hasPermission('view_tasks', ...) the
     * way the old viewableUsers() did — that's specific to the reverse
     * "can this person view the task" lookup (see viewableUsers()'s own
     * docblock for why it re-applies that gate itself), not a concept
     * "who can be assigned" needs.
     *
     * @return Collection<int, User>
     */
    public static function eligibleAssigneesFor(int $organizationId, int $projectId, int $departmentId): Collection
    {
        $candidateIds = collect();

        // 1. Global roles.
        $candidateIds->push(...User::withGlobalRole()->pluck('id'));

        // 2. Management in this org — no department restriction.
        $candidateIds->push(...User::whereHas(
            'orgMemberships',
            fn ($query) => $query->where('organization_id', $organizationId)
                ->whereHas('role', fn ($roleQuery) => $roleQuery->where('slug', Role::MANAGEMENT))
        )->pluck('id'));

        // 3. The project's client(s).
        $project = Project::find($projectId);
        if ($project !== null) {
            $candidateIds->push(...$project->clients()->pluck('users.id'));
        }

        // 4. Staff holding BOTH project_staff membership AND active
        // department access — the intersection, not the union.
        $projectStaffIds = DB::table('project_staff')->where('project_id', $projectId)->pluck('user_id');
        $activeDepartmentAccessIds = AccessPermission::where('organization_id', $organizationId)
            ->where('department_id', $departmentId)
            ->where('allowed', true)
            ->whereHas('department', fn ($query) => $query->where('is_active', true))
            ->pluck('user_id');
        $candidateIds->push(...$projectStaffIds->intersect($activeDepartmentAccessIds));

        $candidateIds = $candidateIds->map(fn ($id) => (int) $id)->unique()->values();

        if ($candidateIds->isEmpty()) {
            return collect();
        }

        return User::whereIn('id', $candidateIds)->orderBy('name')->get();
    }

    /**
     * Every user who would actually pass TaskPolicy::view() for THIS task
     * — the reverse direction of that check (given a task, who can see
     * it, rather than given a user, can they see this task). Not scoped
     * to mentions specifically: any UI needing "who can actually open
     * this task" should reuse this.
     *
     * task #70 (unified eligibility): built on eligibleAssigneesFor()
     * (the same shared rule the Assignee dropdown now uses) — but NOT a
     * pure passthrough. TaskPolicy::view() has two branches assignment
     * eligibility deliberately doesn't need: the task's own current
     * assignee and its subtask assignees can always view/be mentioned
     * about their own task regardless of department access, matching
     * view()'s own unconditional assignee bypass. Dropping this would be
     * a real regression — someone already assigned to a task could stop
     * being mentionable the moment their department access changes, even
     * though they can obviously still open the task they're assigned to
     * (see the "handle already-assigned users who no longer qualify"
     * requirement this whole change is built around). The final
     * hasPermission('view_tasks', ...) filter is likewise specific to
     * this reverse-lookup, mirroring view()'s own first gate — assignment
     * eligibility itself has no such gate.
     *
     * One accepted consequence of sharing eligibleAssigneesFor() as the
     * base: a staff member with department access to this task's
     * department but NOT attached to its project via project_staff is no
     * longer mention-eligible either (the old department-access-alone
     * check here allowed it). This is narrower than before for that one
     * case — the intended result of genuinely unifying the two rules, not
     * an oversight; TaskPolicy::view() itself is unchanged, so that same
     * person can still open the task, just isn't offered as a mention
     * target for it.
     *
     * @return Collection<int, User>
     */
    public function viewableUsers(): Collection
    {
        $candidateIds = static::eligibleAssigneesFor($this->organization_id, $this->project_id, $this->department_id)
            ->pluck('id');

        if ($this->assignee_id !== null) {
            $candidateIds->push($this->assignee_id);
        }

        $candidateIds = $candidateIds
            ->merge($this->subtasks()->whereNotNull('assignee_id')->pluck('assignee_id'))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($candidateIds->isEmpty()) {
            return collect();
        }

        // The one gate every branch above still needs, applied via the
        // real method rather than re-derived here — see docblock.
        return User::whereIn('id', $candidateIds)
            ->get()
            ->filter(fn (User $user) => $user->hasPermission('view_tasks', $this->organization_id))
            ->values();
    }
}
