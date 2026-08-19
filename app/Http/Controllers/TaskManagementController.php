<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Department;
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
                'priority' => $request->string('priority'),
                'status' => $request->string('status'),
                'due_date' => $request->input('due_date') ?: null,
            ]);

            foreach ($request->input('subtasks', []) as $title) {
                $task->subtasks()->create(['title' => $title]);
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

        return view('tasks.edit', array_merge([
            'task' => $task->load('subtasks'),
            'project' => $project,
            'projects' => $projects,
            'canEdit' => auth()->user()->can('update', $task),
            'canDeactivate' => auth()->user()->can('delete', $task),
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
            'priority' => $request->string('priority'),
            'status' => $request->string('status'),
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
     * @return array{projectOrganizations: array<int, int>, departmentsByOrganization: array<int, array<int, array{id: int, name: string}>>, staffByOrganization: array<int, array<int, array{id: int, name: string}>>}
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

        $staffByOrganization = OrgMember::whereIn('organization_id', $organizationIds)
            ->whereHas('role', fn ($query) => $query->where('slug', Role::STAFF))
            ->with('user')
            ->get()
            ->groupBy('organization_id')
            ->map(fn ($members) => $members->map(fn ($m) => ['id' => $m->user->id, 'name' => $m->user->name])->values())
            ->all();

        return [
            'projectOrganizations' => $projects->pluck('organization_id', 'id')->all(),
            'departmentsByOrganization' => $departmentsByOrganization,
            'staffByOrganization' => $staffByOrganization,
        ];
    }
}
