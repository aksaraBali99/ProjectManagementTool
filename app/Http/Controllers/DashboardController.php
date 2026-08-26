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
                'staffOptions' => collect(),
                'selectedStaffIds' => [],
                'emptyMessage' => $user->boardAccessDeniedReason('view_dashboard')->message(),
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $tasks = Task::visibleTo($user, $organization->id)
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
     *     checkbox filter to specific Staff-role members.
     *
     * @param  Collection<int, Task>  $tasks
     * @return array{0: Collection<int, Task>, 1: string, 2: Collection<int, User>, 3: array<int, int>}
     */
    private function myTaskSection(Request $request, User $user, int $organizationId, Collection $tasks): array
    {
        if ($user->isSuperAdmin() || $user->isOwner()) {
            $staffOptions = $this->staffInOrg($organizationId);
            $selectedStaffIds = Collection::make($request->input('staff', []))->map(fn ($id) => (int) $id)->all();

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
            $allowedDepartmentIds = $user->allowedDepartmentIds($organizationId);
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
