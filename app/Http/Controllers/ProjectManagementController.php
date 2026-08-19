<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Models\Organization;
use App\Models\Project;
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
            'templatePriority' => $project->priority,
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        Gate::authorize('create', [Project::class, $request->integer('organization_id')]);

        $project = Project::create([
            'organization_id' => $request->integer('organization_id'),
            'name' => $request->string('name'),
            'description' => $request->string('description'),
            'client_name' => $request->string('client'),
            'is_external' => $this->isExternalClient($request->string('client')),
            'status' => $request->string('status'),
            'priority' => $request->string('priority'),
        ]);

        $project->staff()->sync($request->input('staff', []));

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
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $project->update([
            'name' => $request->string('name'),
            'description' => $request->string('description'),
            'client_name' => $request->string('client'),
            'is_external' => $this->isExternalClient($request->string('client')),
            'status' => $request->string('status'),
            'priority' => $request->string('priority'),
        ]);

        $project->staff()->sync($request->input('staff', []));

        return redirect()->route('projects.index', $project->organization_id)->with('status', 'Project updated.');
    }

    private function isExternalClient(string $client): bool
    {
        return strtolower(trim($client)) !== 'internal';
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
        ], $prefill));
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
