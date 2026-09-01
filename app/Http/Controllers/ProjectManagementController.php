<?php

namespace App\Http\Controllers;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
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
    use ResolvesCurrentOrganization;

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

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        // Visibility (who can see which projects at all) is a policy check,
        // not a query scope, so the whole company's projects are loaded
        // once and policy-filtered here — everything downstream (the
        // filter option lists, the filters themselves, sorting) works off
        // this already-visible set rather than re-querying per filter.
        $visibleProjects = Project::where('organization_id', $organization->id)
            ->withCount('tasks')
            ->with('clients')
            ->get()
            ->filter(fn (Project $project) => $user->can('view', $project))
            ->values();

        $filters = request()->only(['q', 'client_id', 'status', 'priority']);

        $projects = $visibleProjects;

        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $needle = strtolower($search);
            $projects = $projects->filter(fn (Project $project) => str_contains(strtolower($project->name), $needle))->values();
        }

        if ($clientId = request()->string('client_id')->toString()) {
            if ($clientId === 'internal') {
                $projects = $projects->filter(fn (Project $project) => $project->clients->isEmpty())->values();
            } elseif (ctype_digit($clientId)) {
                $id = (int) $clientId;
                $projects = $projects->filter(fn (Project $project) => $project->clients->contains('id', $id))->values();
            }
        }

        if ($status = request()->string('status')->toString()) {
            $projects = $projects->filter(fn (Project $project) => $project->status->value === $status)->values();
        }

        if ($priority = request()->string('priority')->toString()) {
            $projects = $projects->filter(fn (Project $project) => $project->priority->value === $priority)->values();
        }

        $sortable = ['name', 'client', 'status', 'priority', 'tasks'];
        $sort = request()->string('sort')->toString();
        $sort = in_array($sort, $sortable, true) ? $sort : 'name';
        $direction = request()->string('direction')->toString() === 'desc' ? 'desc' : 'asc';

        $projects = $this->sortProjects($projects, $sort, $direction);

        $filterClients = $visibleProjects->pluck('clients')->flatten()->unique('id')->sortBy('name')->values();

        return view('projects.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'projects' => $projects,
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
            'filterClients' => $filterClients,
        ]);
    }

    /**
     * Sorted at the collection level, not via the query builder, since
     * visibility here is a policy check rather than a scope — the whole
     * company's projects are already loaded into memory before the policy
     * filter runs. Client sorts by the client's name, with "Internal"
     * treated as a literal sortable value (not a missing one), since it's
     * a deliberate category rather than an absence of data. Ties break on
     * name so the list order stays stable and predictable.
     */
    private function sortProjects(Collection $projects, string $sort, string $direction): Collection
    {
        $statusRank = array_flip(array_map(fn ($case) => $case->value, ProjectStatus::cases()));
        $priorityRank = array_flip(array_map(fn ($case) => $case->value, Priority::cases()));

        $valueFor = function (Project $project) use ($sort, $statusRank, $priorityRank) {
            return match ($sort) {
                'client' => strtolower($project->primaryClient()->name ?? 'internal'),
                'status' => $statusRank[$project->status->value] ?? PHP_INT_MAX,
                'priority' => $priorityRank[$project->priority->value] ?? PHP_INT_MAX,
                'tasks' => $project->tasks_count,
                default => strtolower($project->name),
            };
        };

        return $projects->sort(function (Project $a, Project $b) use ($valueFor, $direction) {
            $result = $valueFor($a) <=> $valueFor($b);
            if ($direction === 'desc') {
                $result = -$result;
            }

            return $result !== 0 ? $result : strtolower($a->name) <=> strtolower($b->name);
        })->values();
    }

    public function create(?Organization $organization = null): View
    {
        $manageableOrgs = Organization::whereIn('id', auth()->user()->manageableOrganizationIds())
            ->orderBy('name')
            ->get();

        abort_if($manageableOrgs->isEmpty(), 403);

        $organization = $this->resolveCurrentOrganization($manageableOrgs, $organization);

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
            'members' => $this->assignableStaff($organization),
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
            $org->id => $this->assignableStaff($org)
                ->map(fn ($member) => ['id' => $member->id, 'name' => $member->name])
                ->values(),
        ])->all();
    }

    /**
     * Company members eligible for the Project "Assigned staff" picker —
     * every org_member except those holding the Client role there. Client
     * visibility on a project is granted separately via the Client field
     * (project_clients), not by being added as staff.
     *
     * @return Collection<int, User>
     */
    private function assignableStaff(Organization $organization): Collection
    {
        $clientRoleId = Role::where('slug', Role::CLIENT)->value('id');

        return $organization->members()
            ->wherePivot('role_id', '!=', $clientRoleId)
            ->orderBy('name')
            ->get(['users.id', 'users.name']);
    }
}
