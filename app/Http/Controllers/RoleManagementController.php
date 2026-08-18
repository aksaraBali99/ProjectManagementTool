<?php

namespace App\Http\Controllers;

use App\Http\Requests\Roles\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class RoleManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Role::class);

        return view('roles.index', ['roles' => Role::withCount('users')->orderBy('name')->get()]);
    }

    public function edit(Role $role): View
    {
        Gate::authorize('update', $role);

        return view('roles.edit', ['role' => $role]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        Gate::authorize('update', $role);

        $role->update($request->only(array_filter(['name', 'description', $role->is_system ? null : 'slug'])));

        return redirect()->route('roles.index')->with('status', 'Role updated.');
    }
}
