<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Document;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskManagementController extends Controller
{
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

        if (! $organization || ! $organizations->contains('id', $organization->id)) {
            $organization = $organizations->first();
        }

        $showInactive = request()->boolean('show_inactive');

        $query = Task::where('organization_id', $organization->id)
            ->with(['project', 'department', 'assignee', 'subtasks', 'comments.user']);

        if ($showInactive) {
            $query->withTrashed();
        }

        $isManagerHere = $user->isSuperAdmin() || $user->isOwner() || $user->isManagementInOrg($organization->id);

        if (! $isManagerHere) {
            if ($user->isClientInOrg($organization->id)) {
                $clientProjectIds = $user->projectsAsClient()->where('organization_id', $organization->id)->pluck('projects.id');

                $query->whereIn('project_id', $clientProjectIds);
            } else {
                $allowedDepartmentIds = AccessPermission::where('user_id', $user->id)
                    ->where('organization_id', $organization->id)
                    ->where('allowed', true)
                    ->pluck('department_id');

                $query->whereIn('department_id', $allowedDepartmentIds);
            }
        }

        $tasks = $query->orderBy('due_date')->orderBy('title')->get();

        $projectsInList = $tasks->pluck('project')->filter()->unique('id')->values();

        return view('tasks.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'tasks' => $tasks,
            'showInactive' => $showInactive,
            'canCreate' => Gate::allows('create', [Task::class, $organization->id]),
            'canAddDocuments' => Gate::allows('create', [Document::class, $organization->id]),
            'staffByProject' => $this->staffOptionsByProject($projectsInList),
        ]);
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

        Gate::authorize('create', [Task::class, $project->organization_id]);

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

        $attachedDocuments = $task->documents()->orderBy('name')->get();

        $availableDocuments = Document::where('organization_id', $task->organization_id)
            ->whereNotIn('id', $attachedDocuments->pluck('id'))
            ->get()
            ->filter(fn (Document $document) => Gate::allows('view', $document))
            ->sortBy('name')
            ->values();

        return view('tasks.edit', array_merge([
            'task' => $task->load('subtasks'),
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
            'title' => $request->string('title'),
            'description' => $request->string('description'),
            'priority' => $request->input('priority'),
            'status' => $request->input('status'),
            'due_date' => $request->input('due_date') ?: null,
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
     * @param  Collection<int, Project>  $projects
     * @return array{projectOrganizations: array<int, int>, departmentsByOrganization: array<int, array<int, array{id: int, name: string}>>, staffByProject: array<int, array<int, array{id: int, name: string}>>}
     */
    private function cascadingOptions(Collection $projects): array
    {
        $organizationIds = $projects->pluck('organization_id')->unique()->values();

        $departmentsByOrganization = Department::whereIn('organization_id', $organizationIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
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
     * Assignee options for a task/subtask are staff who are BOTH staff-role
     * in the project's company AND actually assigned to that project (via
     * project_staff) — not every staff member company-wide. Joins the pivot
     * directly rather than relying on an eager-loaded `staff` relation, so
     * this works whether $projects is an Eloquent or a plain Support
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

        $organizationIds = $projects->pluck('organization_id')->unique()->values();

        $staffUserIdsByOrg = OrgMember::whereIn('organization_id', $organizationIds)
            ->whereHas('role', fn ($query) => $query->where('slug', Role::STAFF))
            ->get(['organization_id', 'user_id'])
            ->groupBy('organization_id')
            ->map(fn ($rows) => $rows->pluck('user_id'));

        $projectStaffByProject = DB::table('project_staff')
            ->join('users', 'users.id', '=', 'project_staff.user_id')
            ->whereIn('project_staff.project_id', $projects->pluck('id'))
            ->get(['project_staff.project_id', 'users.id', 'users.name'])
            ->groupBy('project_id');

        return $projects->mapWithKeys(function (Project $project) use ($projectStaffByProject, $staffUserIdsByOrg) {
            $allowedIds = $staffUserIdsByOrg->get($project->organization_id, collect());

            $members = ($projectStaffByProject->get($project->id) ?? collect())
                ->filter(fn ($row) => $allowedIds->contains($row->id))
                ->sortBy('name')
                ->map(fn ($row) => ['id' => $row->id, 'name' => $row->name])
                ->values();

            return [$project->id => $members];
        })->all();
    }
}
