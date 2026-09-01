<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskManagementController extends Controller
{
    use ResolvesCurrentOrganization;

    public function index(?Organization $organization = null): View
    {
        Gate::authorize('viewAny', Task::class);

        $user = auth()->user();
        $organizations = Organization::whereIn('id', $user->visibleOrganizationIds())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('tasks.index', [
                'organizations' => $organizations,
                'organization' => null,
                'tasks' => collect(),
                'showInactive' => false,
                'canCreate' => false,
                'canAddDocuments' => false,
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $showInactive = request()->boolean('show_inactive');

        $visibleScope = fn () => Task::visibleTo($user, $organization->id);

        $query = $visibleScope()->with(['project', 'department', 'assignee', 'subtasks', 'comments.user']);

        if ($showInactive) {
            $query->withTrashed();
        }

        $filters = request()->only(['q', 'project_id', 'department_id', 'assignee_id', 'priority', 'status', 'due_from', 'due_to']);

        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $query->where('title', 'like', '%'.$search.'%');
        }

        if ($projectId = request()->integer('project_id')) {
            $query->where('project_id', $projectId);
        }

        if ($departmentId = request()->integer('department_id')) {
            $query->where('department_id', $departmentId);
        }

        if ($assigneeId = request()->string('assignee_id')->toString()) {
            if ($assigneeId === 'unassigned') {
                $query->whereNull('assignee_id');
            } elseif (ctype_digit($assigneeId)) {
                $query->where('assignee_id', (int) $assigneeId);
            }
        }

        if ($priority = request()->string('priority')->toString()) {
            $query->where('priority', $priority);
        }

        if ($status = request()->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($dueFrom = request()->string('due_from')->toString()) {
            $query->whereDate('due_date', '>=', $dueFrom);
        }

        if ($dueTo = request()->string('due_to')->toString()) {
            $query->whereDate('due_date', '<=', $dueTo);
        }

        $sortable = ['title', 'project', 'department', 'assignee', 'priority', 'status', 'due_date', 'active'];
        $sort = request()->string('sort')->toString();
        $sort = in_array($sort, $sortable, true) ? $sort : 'due_date';
        $direction = request()->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $tasks = $this->sortTasks($query->get(), $sort, $direction);

        $projectsInList = $tasks->pluck('project')->filter()->unique('id')->values();

        // Filter option lists reflect what this user can actually see (same
        // visibility scope as the list itself, ignoring the other filters so
        // switching one filter doesn't prune the others' choices) rather
        // than every project/department/user in the company, so a staff
        // member never sees a department they don't have access to sitting
        // in the dropdown.
        $optionsScope = $visibleScope();
        if ($showInactive) {
            $optionsScope->withTrashed();
        }
        $optionProjectIds = (clone $optionsScope)->distinct()->pluck('project_id');
        $optionDepartmentIds = (clone $optionsScope)->distinct()->pluck('department_id');
        $optionAssigneeIds = (clone $optionsScope)->whereNotNull('assignee_id')->distinct()->pluck('assignee_id');

        return view('tasks.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'tasks' => $tasks,
            'showInactive' => $showInactive,
            'canCreate' => Gate::allows('create', [Task::class, $organization->id]),
            'canAddDocuments' => Gate::allows('create', [Document::class, $organization->id]),
            'staffByProject' => $this->staffOptionsByProject($projectsInList),
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
            'filterProjects' => Project::whereIn('id', $optionProjectIds)->orderBy('name')->get(['id', 'name']),
            'filterDepartments' => Department::whereIn('id', $optionDepartmentIds)->orderBy('name')->get(['id', 'name']),
            'filterAssignees' => User::whereIn('id', $optionAssigneeIds)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Sorted at the collection level, not via the query builder, since
     * project/department/assignee live on related models already eager-
     * loaded for the drilldown rows — joining for those would duplicate
     * what's already in memory. A missing value (no due date, no assignee)
     * always sorts last regardless of direction; ties break on title so the
     * list order stays stable and predictable.
     */
    private function sortTasks(Collection $tasks, string $sort, string $direction): Collection
    {
        $priorityRank = array_flip(array_map(fn ($case) => $case->value, Priority::cases()));
        $statusRank = array_flip(array_map(fn ($case) => $case->value, TaskStatus::cases()));

        $valueFor = function (Task $task) use ($sort, $priorityRank, $statusRank) {
            return match ($sort) {
                'project' => strtolower($task->project->name ?? ''),
                'department' => strtolower($task->department->name ?? ''),
                'assignee' => strtolower($task->assignee->name ?? ''),
                'priority' => $priorityRank[$task->priority->value] ?? PHP_INT_MAX,
                'status' => $statusRank[$task->status->value] ?? PHP_INT_MAX,
                'active' => $task->trashed() ? 0 : 1,
                'due_date' => $task->due_date?->timestamp,
                default => strtolower($task->title),
            };
        };

        return $tasks->sort(function (Task $a, Task $b) use ($valueFor, $direction) {
            $valueA = $valueFor($a);
            $valueB = $valueFor($b);

            $missingA = $valueA === null || $valueA === '';
            $missingB = $valueB === null || $valueB === '';
            if ($missingA !== $missingB) {
                return $missingA ? 1 : -1;
            }

            $result = $valueA <=> $valueB;
            if ($direction === 'desc') {
                $result = -$result;
            }

            return $result !== 0 ? $result : strtolower($a->title) <=> strtolower($b->title);
        })->values();
    }

    public function create(?Project $project = null): View
    {
        $manageableOrgIds = auth()->user()->manageableOrganizationIds();
        abort_if(empty($manageableOrgIds), 403);

        $projects = Project::whereIn('organization_id', $manageableOrgIds)->orderBy('name')->get();

        if ($projects->isEmpty()) {
            return view('tasks.create', [
                'projects' => $projects,
                'project' => null,
            ]);
        }

        if (! $project || ! $projects->contains('id', $project->id)) {
            $project = $projects->first();
        }

        Gate::authorize('create', [Task::class, $project->organization_id]);

        return view('tasks.create', array_merge([
            'projects' => $projects,
            'project' => $project,
        ], $this->cascadingOptions($projects)));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $project = Project::findOrFail($request->integer('project_id'));

        Gate::authorize('create', [Task::class, $project->organization_id, $request->integer('department_id')]);

        $task = DB::transaction(function () use ($request, $project) {
            $task = Task::create([
                'organization_id' => $project->organization_id,
                'project_id' => $project->id,
                'department_id' => $request->integer('department_id'),
                'assignee_id' => $request->input('assignee_id') ?: null,
                'title' => $request->string('title'),
                'description' => $request->string('description'),
                'priority' => $request->input('priority'),
                'status' => $request->input('status'),
                'due_date' => $request->input('due_date') ?: null,
            ]);

            foreach ($request->input('subtasks', []) as $subtask) {
                $task->subtasks()->create([
                    'title' => $subtask['title'],
                    'assignee_id' => $subtask['assignee_id'] ?? null,
                    'due_date' => $subtask['due_date'] ?? null,
                ]);
            }

            return $task;
        });

        return redirect()->route('tasks.edit', $task)->with('status', 'Task created.');
    }

    public function edit(Task $task): View
    {
        Gate::authorize('view', $task);

        $project = $task->project;
        $manageableOrgIds = auth()->user()->manageableOrganizationIds();
        $projects = Project::whereIn('organization_id', $manageableOrgIds)->orderBy('name')->get();

        if (! $projects->contains('id', $project->id)) {
            $projects->push($project);
        }

        $allAttachedDocuments = $task->documents()->orderBy('name')->get();

        // A document attached to this task isn't automatically visible to
        // everyone who can see the task — e.g. a private document
        // management attached stays hidden from a staff assignee who
        // isn't its uploader, same as it would be on the Documents page
        // itself.
        $attachedDocuments = $allAttachedDocuments
            ->filter(fn (Document $document) => Gate::allows('view', $document))
            ->values();

        $availableDocuments = Document::where('organization_id', $task->organization_id)
            ->whereNotIn('id', $allAttachedDocuments->pluck('id'))
            ->get()
            ->filter(fn (Document $document) => Gate::allows('view', $document))
            ->sortBy('name')
            ->values();

        return view('tasks.edit', array_merge([
            'task' => $task->load('subtasks', 'comments.user'),
            'project' => $project,
            'projects' => $projects,
            'canEdit' => auth()->user()->can('update', $task),
            'canDeactivate' => auth()->user()->can('delete', $task),
            'attachedDocuments' => $attachedDocuments,
            'availableDocuments' => $availableDocuments,
        ], $this->cascadingOptions($projects)));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        Gate::authorize('update', $task);

        $project = Project::findOrFail($request->integer('project_id'));

        $task->update([
            'organization_id' => $project->organization_id,
            'project_id' => $project->id,
            'department_id' => $request->integer('department_id'),
            'assignee_id' => $request->input('assignee_id') ?: null,
            'title' => (string) $request->string('title'),
            'description' => (string) $request->string('description'),
            'priority' => $request->input('priority'),
            'status' => $request->input('status'),
            'due_date' => $request->input('due_date') ?: null,
            'start_date' => $request->input('start_date') ?: null,
        ]);

        return redirect()->route('tasks.edit', $task)->with('status', 'Task updated.');
    }

    public function toggleActive(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        if ($task->trashed()) {
            $task->restore();
            $status = 'Task activated.';
        } else {
            $task->delete();
            $status = 'Task deactivated.';
        }

        return redirect()->route('tasks.edit', $task)->with('status', $status);
    }

    /**
     * Kanban's drag-and-drop (and its dropdown fallback) both hit this
     * endpoint — authorized via TaskPolicy::updateStatus() (its own
     * update_kanban_cards permission, OR being the task's own assignee),
     * not the general task-edit ability, so a drag isn't a side door
     * around a rule a form submit already enforces, and "who can move
     * cards on Kanban" is independently configurable from "who can edit
     * the full task form".
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('updateStatus', $task);

        $data = $request->validate([
            'status' => ['required', Rule::enum(TaskStatus::class)],
        ]);

        $task->update(['status' => $data['status']]);

        return response()->json([
            'task' => ['id' => $task->id, 'status' => $task->status->value, 'status_label' => $task->status->label()],
        ]);
    }

    /**
     * @param  Collection<int, Project>  $projects
     * @return array{projectOrganizations: array<int, int>, departmentsByOrganization: array<int, array<int, array{id: int, name: string}>>, staffByProject: array<int, array<int, array{id: int, name: string}>>}
     */
    private function cascadingOptions(Collection $projects): array
    {
        $user = auth()->user();
        $organizationIds = $projects->pluck('organization_id')->unique()->values();

        // Management/global roles see every active department; a staff
        // member only sees the departments they've actually been granted,
        // so the dropdown can't offer a department store()'s Gate check
        // would then reject.
        $departmentsByOrganization = Department::whereIn('organization_id', $organizationIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->filter(fn (Department $department) => $user->isSuperAdmin() || $user->isOwner()
                || $user->isManagementInOrg($department->organization_id)
                || $user->hasDepartmentAccess($department->organization_id, $department->id))
            ->groupBy('organization_id')
            ->map(fn ($departments) => $departments->map(fn ($d) => ['id' => $d->id, 'name' => $d->name])->values())
            ->all();

        return [
            'projectOrganizations' => $projects->pluck('organization_id', 'id')->all(),
            'departmentsByOrganization' => $departmentsByOrganization,
            'staffByProject' => $this->staffOptionsByProject($projects),
        ];
    }

    /**
     * Assignee options for a task/subtask are anyone attached to the
     * project — via project_staff (any role: management, staff, ...) or
     * project_clients (the project's client) — not scoped to a "Staff"
     * role, and not every company member company-wide. Joins the pivots
     * directly rather than relying on eager-loaded relations, so this
     * works whether $projects is an Eloquent or a plain Support
     * collection (e.g. Task::with('project')->get()->pluck('project')).
     *
     * @param  Collection<int, Project>  $projects
     * @return array<int, array<int, array{id: int, name: string}>> keyed by project id
     */
    private function staffOptionsByProject(Collection $projects): array
    {
        if ($projects->isEmpty()) {
            return [];
        }

        $projectIds = $projects->pluck('id');

        $staffRows = DB::table('project_staff')
            ->join('users', 'users.id', '=', 'project_staff.user_id')
            ->whereIn('project_staff.project_id', $projectIds)
            ->get(['project_staff.project_id', 'users.id', 'users.name']);

        $clientRows = DB::table('project_clients')
            ->join('users', 'users.id', '=', 'project_clients.user_id')
            ->whereIn('project_clients.project_id', $projectIds)
            ->get(['project_clients.project_id', 'users.id', 'users.name']);

        $membersByProject = $staffRows->concat($clientRows)->groupBy('project_id');

        return $projects->mapWithKeys(function (Project $project) use ($membersByProject) {
            $members = ($membersByProject->get($project->id) ?? collect())
                ->unique('id')
                ->sortBy('name')
                ->map(fn ($row) => ['id' => $row->id, 'name' => $row->name])
                ->values();

            return [$project->id => $members];
        })->all();
    }
}
