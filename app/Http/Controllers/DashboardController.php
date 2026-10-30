<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ResolvesCurrentOrganization;

    public function __invoke(Request $request, ?Organization $organization = null): View
    {
        $user = auth()->user();

        $organizations = Organization::whereIn('id', $user->boardOrganizationIds('view_dashboard'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('dashboard', [
                'organizations' => $organizations,
                'organization' => null,
                'priorityGroups' => collect(),
                'activeTasks' => collect(),
                'myTasks' => collect(),
                'myTaskMode' => 'none',
                'myTasksLink' => null,
                'staffOptions' => collect(),
                'selectedStaffIds' => [],
                'emptyMessage' => $user->boardAccessDeniedReason('view_dashboard')->message(),
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        // task #72 phase 7: completed tasks older than the company's window
        // drop out of every section below (priority groups, Active, My
        // Task); each section links to the Tasks list to see them.
        $tasks = Task::visibleTo($user, $organization->id)
            ->openOrCompletedWithin($organization->completedWindowStart())
            ->with(['project', 'department', 'assignee', 'subtasks'])
            ->get();

        $priorityGroups = collect(Priority::cases())->mapWithKeys(
            fn (Priority $priority) => [$priority->value => $tasks->where('priority', $priority)->sortBy('due_date')->values()]
        );

        $activeStatuses = [TaskStatus::InProgress, TaskStatus::InReview];
        $activeTasks = $tasks
            ->filter(fn (Task $task) => in_array($task->status, $activeStatuses, true) && $task->priority === Priority::High)
            ->sortBy('due_date')
            ->values();

        [$myTasks, $myTaskMode, $staffOptions, $selectedStaffIds] = $this->myTaskSection($request, $user, $organization->id, $tasks);

        return view('dashboard', [
            'organizations' => $organizations,
            'organization' => $organization,
            'priorityGroups' => $priorityGroups,
            'activeTasks' => $activeTasks,
            'myTasks' => $myTasks,
            'myTaskMode' => $myTaskMode,
            'myTasksLink' => $this->myTasksLink($user, $organization, $myTaskMode, $selectedStaffIds),
            'staffOptions' => $staffOptions,
            'selectedStaffIds' => $selectedStaffIds,
        ]);
    }

    /**
     * MyTask is a narrower FILTER on top of the general visibility already
     * applied to $tasks, not a replacement for it — scoped per role:
     *   - staff: assigned to them (directly, OR via one of the task's
     *     subtasks — being a subtask's assignee is its own inclusion path,
     *     same as the general Task List/TaskPolicy precedent), further
     *     constrained to departments they have access to (stricter than
     *     the general assignee-anywhere bypass $tasks may already
     *     include).
     *   - management: assigned to Staff-role members of this company.
     *   - super_admin/owner: every task in this company, narrowable via a
     *     checkbox filter to specific Staff-role members OR any global-role
     *     user (super_admin/owner) — the latter can now be a task assignee
     *     too (see User::scopeAssignableAsStaffIn()), so the filter needs
     *     to be able to isolate their tasks the same way it already can
     *     for Staff-role members.
     *
     * @param  Collection<int, Task>  $tasks
     * @return array{0: Collection<int, Task>, 1: string, 2: Collection<int, User>, 3: array<int, int>}
     */
    private function myTaskSection(Request $request, User $user, int $organizationId, Collection $tasks): array
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            $staffOptions = $this->staffInOrg($organizationId)
                ->concat(User::withGlobalRole()->get(['id', 'name']))
                ->unique('id')
                ->sortBy('name')
                ->values();
            // values(): the request's own keys are kept otherwise, so
            // ?staff[1]=5 would arrive as [1 => 5] (task #72 phase 7's My
            // Task link reads the first id).
            $selectedStaffIds = Collection::make($request->input('staff', []))->map(fn ($id) => (int) $id)->values()->all();

            $myTasks = empty($selectedStaffIds)
                ? $tasks->values()
                : $tasks->whereIn('assignee_id', $selectedStaffIds)->values();

            return [$myTasks->sortBy('due_date')->values(), 'admin', $staffOptions, $selectedStaffIds];
        }

        if ($user->isManagementInOrg($organizationId)) {
            $staffIds = $this->staffInOrg($organizationId)->pluck('id');
            $myTasks = $tasks->whereIn('assignee_id', $staffIds)->sortBy('due_date')->values();

            return [$myTasks, 'management', collect(), []];
        }

        if ($user->isStaffInOrg($organizationId)) {
            // task #73 (visibility consolidation): kept, NOT removed as
            // redundant with the scopeVisibleTo() fix — this is doing
            // genuinely separate work. scopeVisibleTo() has an
            // unconditional assignee-anywhere bypass (a task assigned to
            // this staff member is visible even in a department they were
            // never granted, not just a deactivated one); MyTask is
            // deliberately narrower than that general visibility —
            // "assigned to me AND within a department I actually have
            // access to" — so this filter excludes exactly the
            // assignee-bypass tasks outside a granted department that
            // $tasks legitimately still includes. Confirmed by reverting
            // this once: it broke two pre-existing tests asserting exactly
            // that narrower MyTask behavior, for reasons unrelated to
            // either scopeVisibleTo() gap.
            //
            // task #73 (department-null crash fix): allowedActiveDepartmentIds(),
            // not the bare allowedDepartmentIds() this used to call — a
            // deactivated department no longer counts as "a department I
            // have access to" for MyTask, same as everywhere else
            // (hasDepartmentAccess(), Task::scopeVisibleTo()). Without
            // this, a task assigned directly to this staff member in a
            // department an owner has since deactivated stayed in MyTask
            // indefinitely.
            $allowedDepartmentIds = $user->allowedActiveDepartmentIds($organizationId);
            $myTasks = $tasks
                ->filter(fn (Task $task) => $allowedDepartmentIds->contains($task->department_id)
                    && ($task->assignee_id === $user->id || $task->subtasks->contains('assignee_id', $user->id)))
                ->sortBy('due_date')
                ->values();

            return [$myTasks, 'staff', collect(), []];
        }

        return [collect(), 'none', collect(), []];
    }

    /**
     * task #72 phase 7: where My Task's "see everything" link goes — the
     * Tasks list with every completed task included, narrowed to the same
     * person when a single Tasks-list assignee filter expresses the
     * section: staff (their own tasks) or an owner with exactly one person
     * ticked. Management's "any staff member", several people ticked, or
     * nobody ticked have no single filter, so they open the whole list.
     *
     * Staff's section also includes tasks where only a SUBTASK is theirs;
     * the Tasks list's assignee filter matches the task's own assignee, so
     * those are the one thing the link won't narrow to.
     *
     * @param  array<int, int>  $selectedStaffIds
     * @return array{url: string, label: string}|null
     */
    private function myTasksLink(User $user, Organization $organization, string $myTaskMode, array $selectedStaffIds): ?array
    {
        $assigneeId = match (true) {
            $myTaskMode === 'staff' => $user->id,
            $myTaskMode === 'admin' && count($selectedStaffIds) === 1 => $selectedStaffIds[0],
            default => null,
        };

        return match (true) {
            $myTaskMode === 'none' => null,
            $assigneeId !== null => [
                'url' => route('tasks.index', [$organization, 'assignee_id' => $assigneeId, 'completed' => 'all']),
                'label' => 'View all in Tasks list',
            ],
            default => [
                'url' => route('tasks.index', [$organization, 'completed' => 'all']),
                'label' => 'Open in Tasks list',
            ],
        };
    }

    /**
     * @return Collection<int, User>
     */
    private function staffInOrg(int $organizationId): Collection
    {
        return User::whereHas(
            'orgMemberships',
            fn ($query) => $query->where('organization_id', $organizationId)
                ->whereHas('role', fn ($roleQuery) => $roleQuery->where('slug', Role::STAFF))
        )->orderBy('name')->get(['id', 'name']);
    }
}
