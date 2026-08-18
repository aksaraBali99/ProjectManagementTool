<?php

namespace App\Http\Controllers;

use App\Http\Requests\Departments\StoreDepartmentRequest;
use App\Http\Requests\Departments\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DepartmentManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Department::class);

        return view('departments.index', [
            'departments' => Department::with('organization')->withCount('tasks')->orderBy('name')->get(),
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

        Department::create($request->only('organization_id', 'name', 'color'));

        return redirect()->route('departments.index')->with('status', 'Department created.');
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

        return redirect()->route('departments.index')->with('status', 'Department updated.');
    }

    public function toggleActive(Department $department): RedirectResponse
    {
        Gate::authorize('update', $department);

        $department->update(['is_active' => ! $department->is_active]);

        return redirect()->route('departments.index')
            ->with('status', $department->is_active ? 'Department activated.' : 'Department deactivated.');
    }
}
