<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Http\Requests\Departments\StoreDepartmentRequest;
use App\Http\Requests\Departments\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DepartmentManagementController extends Controller
{
    use ResolvesCurrentOrganization;

    public function index(?Organization $organization = null): View
    {
        Gate::authorize('viewAny', Department::class);

        $organizations = Organization::where('is_active', true)->orderBy('name')->get();

        if ($organizations->isEmpty()) {
            return view('departments.index', [
                'organizations' => $organizations,
                'organization' => null,
                'departments' => collect(),
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $departments = Department::where('organization_id', $organization->id)
            ->withCount('tasks')
            ->orderBy('name')
            ->get();

        return view('departments.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'departments' => $departments,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Department::class);

        return view('departments.create', ['organizations' => Organization::orderBy('name')->get()]);
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Gate::authorize('create', Department::class);

        $organizationIds = $request->input('organization_ids');

        // A unique-constraint failure partway through (the same department
        // name already exists for a later company in the list) previously
        // left the earlier creates committed and the rest silently missing
        // — wrapped so it's all-or-nothing, matching the equivalent
        // multi-step write in UserManagementController.
        DB::transaction(function () use ($request, $organizationIds) {
            foreach ($organizationIds as $organizationId) {
                Department::create([
                    'organization_id' => $organizationId,
                    'name' => $request->string('name'),
                    'color' => $request->string('color'),
                ]);
            }
        });

        $status = count($organizationIds) > 1
            ? 'Department created for '.count($organizationIds).' companies.'
            : 'Department created.';

        return redirect()->route('departments.index', $organizationIds[0])->with('status', $status);
    }

    public function edit(Department $department): View
    {
        Gate::authorize('update', $department);

        return view('departments.edit', [
            'department' => $department,
            'organizations' => Organization::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        Gate::authorize('update', $department);

        $department->update($request->only('organization_id', 'name', 'color'));

        return redirect()->route('departments.index', $department->organization_id)->with('status', 'Department updated.');
    }

    public function toggleActive(Department $department): RedirectResponse
    {
        Gate::authorize('update', $department);

        $department->update(['is_active' => ! $department->is_active]);

        return redirect()->route('departments.index', $department->organization_id)
            ->with('status', $department->is_active ? 'Department activated.' : 'Department deactivated.');
    }
}
