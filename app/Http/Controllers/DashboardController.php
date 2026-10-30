<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use ResolvesCurrentOrganization;

    /**
     * task #72 phase 8: each section shows its first this-many tasks in
     * Task::scopeOrderByDueSoonest() order; the header shows the full count
     * and the link below opens the rest in the Tasks list.
     */
    public const SECTION_LIMIT = 10;

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
                'sectionCounts' => [],
                'sectionLinks' => [],
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
        // Task) and out of every count; each section links to the Tasks
        // list, whose default "Recent" mode applies the same window.
        //
        // Built once and cloned per section: for staff and clients,
        // scopeVisibleTo() itself queries (granted departments, client
        // projects), and would repeat that for every section.
        $visibleBase = Task::visibleTo($user, $organization->id)->openOrCompletedWithin($organization->completedWindowStart());
        $visible = fn (): Builder => clone $visibleBase;

        $activeStatuses = [TaskStatus::InProgress->value, TaskStatus::InReview->value];

        // One grouped count for the three priorities, carrying Active (High
        // and In progress / In review) along as a conditional sum.
        $countsByPriority = $visible()->toBase()
            ->selectRaw('priority, count(*) as total, sum(case when status in (?, ?) then 1 else 0 end) as active', $activeStatuses)
            ->groupBy('priority')
            ->get()
            ->keyBy('priority');

        $sectionCounts = ['active' => (int) ($countsByPriority[Priority::High->value]->active ?? 0)];
        foreach (Priority::cases() as $priority) {
            $sectionCounts[$priority->value] = (int) ($countsByPriority[$priority->value]->total ?? 0);
        }

        $activeTasks = $visible()
            ->where('priority', Priority::High->value)
            ->whereIn('status', $activeStatuses)
            ->orderByDueSoonest()
            ->limit(self::SECTION_LIMIT)
            ->get();

        $priorityGroups = collect(Priority::cases())->mapWithKeys(fn (Priority $priority) => [
            $priority->value => $visible()
                ->where('priority', $priority->value)
                ->orderByDueSoonest()
                ->limit(self::SECTION_LIMIT)
                ->get(),
        ]);

        [$myTaskQuery, $myTaskMode, $staffOptions, $selectedStaffIds] = $this->myTaskSection($request, $user, $organization->id, $visible());

        $myTasks = new EloquentCollection;
        if ($myTaskQuery) {
            // Owner with nobody ticked: the section is every task the other
            // sections count, and tasks.priority is NOT NULL (an enum with a
            // default), so the priority counts already add up to it.
            $sectionCounts['my'] = $myTaskMode === 'admin' && empty($selectedStaffIds)
                ? array_sum(array_map(fn (Priority $priority) => $sectionCounts[$priority->value], Priority::cases()))
                : (clone $myTaskQuery)->count();

            $myTasks = $myTaskQuery->orderByDueSoonest()->limit(self::SECTION_LIMIT)->get();
        }

        // One eager load for everything rendered (at most 5 x SECTION_LIMIT
        // tasks); a task in two sections is two instances, both filled.
        (new EloquentCollection($priorityGroups->flatten(1)->all()))
            ->concat($activeTasks)
            ->concat($myTasks)
            ->load(['project', 'department', 'assignee']);

        if ($myTaskMode === 'staff') {
            // ONLY this user's subtasks, not all of them: the one reader is
            // the "Your subtask: …" line in dashboard.blade.php. Don't reuse
            // $task->subtasks on this page expecting the full list.
            $myTasks->load(['subtasks' => fn ($query) => $query->where('assignee_id', $user->id)]);
        }

        return view('dashboard', [
            'organizations' => $organizations,
            'organization' => $organization,
            'priorityGroups' => $priorityGroups,
            'activeTasks' => $activeTasks,
            'myTasks' => $myTasks,
            'sectionCounts' => $sectionCounts,
            'sectionLinks' => $this->sectionLinks($organization, $sectionCounts),
            'myTaskMode' => $myTaskMode,
            'myTasksLink' => $this->myTasksLink($user, $organization, $myTaskMode, $selectedStaffIds, $sectionCounts['my'] ?? 0),
            'staffOptions' => $staffOptions,
            'selectedStaffIds' => $selectedStaffIds,
        ]);
    }

    /**
     * MyTask is a narrower FILTER on top of the general visibility already
     * applied to $visible, not a replacement for it — scoped per role:
     *   - staff: assigned to them (directly, OR via one of the task's
     *     subtasks — being a subtask's assignee is its own inclusion path,
     *     same as the general Task List/TaskPolicy precedent), further
     *     constrained to departments they have access to (stricter than
     *     the general assignee-anywhere bypass $visible may already
     *     include).
     *   - management: assigned to Staff-role members of this company.
     *   - super_admin/owner: every task in this company, narrowable via a
     *     checkbox filter to specific Staff-role members OR any global-role
     *     user (super_admin/owner) — the latter can now be a task assignee
     *     too (see User::scopeAssignableAsStaffIn()), so the filter needs
     *     to be able to isolate their tasks the same way it already can
     *     for Staff-role members.
     *
     * task #72 phase 8: returns the section's query (unordered, unlimited)
     * instead of filtering an already-loaded collection; null for a client.
     *
     * @return array{0: Builder|null, 1: string, 2: Collection<int, User>, 3: array<int, int>}
     */
    private function myTaskSection(Request $request, User $user, int $organizationId, Builder $visible): array
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

            if (! empty($selectedStaffIds)) {
                $visible->whereIn('assignee_id', $selectedStaffIds);
            }

            return [$visible, 'admin', $staffOptions, $selectedStaffIds];
        }

        if ($user->isManagementInOrg($organizationId)) {
            $staffIds = $this->staffInOrg($organizationId)->pluck('id');

            return [$visible->whereIn('assignee_id', $staffIds), 'management', collect(), []];
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
            // $visible legitimately still includes. Confirmed by reverting
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
            //
            // task #72 phase 8: the same rule in SQL. whereHas('subtasks')
            // reads the relation the PHP filter used to eager-load, and a
            // task with no department fails whereIn just as it failed
            // contains(). The department ids are a subquery, the same one
            // scopeVisibleTo() runs: allowedActiveDepartmentIds() would
            // load the access profile's grants in a query of its own.
            $allowedDepartmentIds = $user->accessPermissions()
                ->where('organization_id', $organizationId)
                ->where('allowed', true)
                ->whereHas('department', fn (Builder $departments) => $departments->where('is_active', true))
                ->select('department_id');

            $visible->whereIn('department_id', $allowedDepartmentIds)
                ->where(fn (Builder $query) => $query->where('assignee_id', $user->id)
                    ->orWhereHas('subtasks', fn (Builder $subtasks) => $subtasks->where('assignee_id', $user->id)));

            return [$visible, 'staff', collect(), []];
        }

        return [null, 'none', collect(), []];
    }

    /**
     * task #72 phase 8: the links under Active and the priority groups. The
     * Tasks list's default "Recent" mode applies the same completed window
     * and the same visibility, so a priority group's link opens exactly its
     * count. It takes one status, so Active (High AND In progress or In
     * review) opens every High task instead, without a number.
     *
     * @param  array<string, int>  $sectionCounts
     * @return array<string, array{url: string, label: string}>
     */
    private function sectionLinks(Organization $organization, array $sectionCounts): array
    {
        $links = [
            'active' => [
                'url' => route('tasks.index', [$organization, 'priority' => Priority::High->value]),
                'label' => 'Open in Tasks list',
            ],
        ];

        foreach (Priority::cases() as $priority) {
            $links[$priority->value] = [
                'url' => route('tasks.index', [$organization, 'priority' => $priority->value]),
                'label' => $this->viewAllLabel($sectionCounts[$priority->value]),
            ];
        }

        return $links;
    }

    /**
     * Where My Task's link goes, and whether its label can carry the count.
     *
     * The number appears only where a Tasks list filter matches the
     * section exactly: an owner with nobody ticked (the whole list) or with
     * exactly one person ticked (that assignee). Staff link to their own
     * assignee filter without a number: their section also includes tasks
     * where only a SUBTASK is theirs, and leaves out tasks assigned to them
     * outside a department they can access, so the list's count differs.
     * Management's "any staff member" and several people ticked have no
     * single filter, so they open the whole list.
     *
     * @param  array<int, int>  $selectedStaffIds
     * @return array{url: string, label: string}|null
     */
    private function myTasksLink(User $user, Organization $organization, string $myTaskMode, array $selectedStaffIds, int $myTaskCount): ?array
    {
        return match (true) {
            $myTaskMode === 'none' => null,
            $myTaskMode === 'staff' => [
                'url' => route('tasks.index', [$organization, 'assignee_id' => $user->id]),
                'label' => 'View all in Tasks list',
            ],
            $myTaskMode === 'admin' && empty($selectedStaffIds) => [
                'url' => route('tasks.index', $organization),
                'label' => $this->viewAllLabel($myTaskCount),
            ],
            $myTaskMode === 'admin' && count($selectedStaffIds) === 1 => [
                'url' => route('tasks.index', [$organization, 'assignee_id' => $selectedStaffIds[0]]),
                'label' => $this->viewAllLabel($myTaskCount),
            ],
            default => [
                'url' => route('tasks.index', $organization),
                'label' => 'Open in Tasks list',
            ],
        };
    }

    /**
     * "View all N" only when the section is hiding some; at SECTION_LIMIT
     * or fewer, every task is already on the Dashboard.
     */
    private function viewAllLabel(int $count): string
    {
        if ($count <= self::SECTION_LIMIT) {
            return 'Open in Tasks list';
        }

        return 'View all '.number_format($count).' in Tasks list';
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
