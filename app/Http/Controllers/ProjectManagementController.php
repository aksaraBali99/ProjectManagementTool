<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectManagementController extends Controller
{
    public function index(?Organization $organization = null): View
    {
        Gate::authorize('viewAny', Project::class);

        $user = auth()->user();

        $organizations = Organization::whereIn('id', $user->visibleOrganizationIds())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($organizations->isEmpty()) {
            return view('projects.index', [
                'organizations' => $organizations,
                'organization' => null,
                'projects' => collect(),
            ]);
        }

        if (! $organization || ! $organizations->contains('id', $organization->id)) {
            $organization = $organizations->first();
        }

        $projects = Project::where('organization_id', $organization->id)
            ->withCount('tasks')
            ->with('clients')
            ->orderBy('name')
            ->get()
            ->filter(fn (Project $project) => $user->can('view', $project))
            ->values();

        return view('projects.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'projects' => $projects,
        ]);
    }

    public function create(?Organization $organization = null): View
    {
        $manageableOrgs = Organization::whereIn('id', auth()->user()->manageableOrganizationIds())
            ->orderBy('name')
            ->get();

        abort_if($manageableOrgs->isEmpty(), 403);

        if (! $organization || ! $manageableOrgs->contains('id', $organization->id)) {
            $organization = $manageableOrgs->first();
        }

        Gate::authorize('create', [Project::class, $organization->id]);

        return $this->renderCreateForm($manageableOrgs, $organization);
    }

    /**
     * Pre-fills the Add Project form with another project's name, description,
     * and priority — the shell only, so the admin can spin up an equivalent
     * project for a different (or the same) company. Status always starts
     * "open", and client/staff are left blank for the admin to fill in fresh.
     */
    public function template(Project $project): View
    {
        Gate::authorize('view', $project);

        $manageableOrgs = Organization::whereIn('id', auth()->user()->manageableOrganizationIds())
            ->orderBy('name')
            ->get();

        abort_if($manageableOrgs->isEmpty(), 403);

        $organization = $manageableOrgs->firstWhere('id', '!=', $project->organization_id)
            ?? $manageableOrgs->first();

        Gate::authorize('create', [Project::class, $organization->id]);

        return $this->renderCreateForm($manageableOrgs, $organization, [
            'templateName' => $project->name.' (Copy)',
            'templateDescription' => $project->description,
            'templatePriority' => $project->priority->value,
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Gate::authorize('create', [Project::class, $request->integer('organization_id')]);

        $project = Project::create([
            'organization_id' => $request->integer('organization_id'),
            'name' => $request->string('name'),
            'description' => $request->string('description'),
            'is_external' => $request->filled('client'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
        ]);

        $project->staff()->sync($request->input('staff', []));
        $project->clients()->sync($request->filled('client') ? [$request->integer('client')] : []);

        return redirect()->route('projects.index', $project->organization_id)->with('status', 'Project created.');
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        $organization = $project->organization;

        return view('projects.edit', [
            'project' => $project,
            'organization' => $organization,
            'members' => $organization->members()->orderBy('name')->get(['users.id', 'users.name']),
            'assignedStaffIds' => $project->staff()->pluck('users.id')->all(),
            'clientOptions' => $this->clientOptions(),
            'assignedClientId' => $project->clients()->value('users.id'),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $project->update([
            'name' => $request->string('name'),
            'description' => $request->string('description'),
            'is_external' => $request->filled('client'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
        ]);

        $project->staff()->sync($request->input('staff', []));
        $project->clients()->sync($request->filled('client') ? [$request->integer('client')] : []);

        return redirect()->route('projects.index', $project->organization_id)->with('status', 'Project updated.');
    }

    /**
     * @param  array{templateName?: string, templateDescription?: string, templatePriority?: string}  $prefill
     */
    private function renderCreateForm(Collection $manageableOrgs, Organization $organization, array $prefill = []): View
    {
        return view('projects.create', array_merge([
            'organizations' => $manageableOrgs,
            'organization' => $organization,
            'organizationMembers' => $this->membersByOrganization($manageableOrgs),
            'clientOptions' => $this->clientOptions(),
        ], $prefill));
    }

    /**
     * Users currently holding the Client role in any company — the Project
     * "Client" picker isn't scoped to the project's own company, so any
     * Client-role user anywhere can be attached.
     *
     * @return Collection<int, User>
     */
    private function clientOptions(): Collection
    {
        return User::whereHas('orgMemberships.role', fn ($query) => $query->where('slug', Role::CLIENT))
            ->orderBy('name')
            ->get(['users.id', 'users.name']);
    }

    /**
     * @return array<int, array<int, array{id: int, name: string}>>
     */
    private function membersByOrganization(Collection $organizations): array
    {
        return $organizations->mapWithKeys(fn (Organization $org) => [
            $org->id => $org->members()->orderBy('name')->get(['users.id', 'users.name'])
                ->map(fn ($member) => ['id' => $member->id, 'name' => $member->name])
                ->values(),
        ])->all();
    }
}
