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

        // Super Admin/Owner are held via the global user_roles pivot
        // (users()); Management/Staff/Client are held per-company via
        // org_members instead (orgMembers()) — a plain withCount('users')
        // only ever sees the former, so the latter three always showed 0
        // regardless of how many people actually held them. Counting
        // distinct user_id, not rows, so a user holding the same role in
        // two companies (e.g. Staff in both) is counted once, not twice.
        $roles = Role::orderBy('name')->get()->each(function (Role $role) {
            $role->users_count = in_array($role->slug, Role::GLOBAL_SLUGS, true)
                ? $role->users()->count()
                : $role->orgMembers()->distinct('user_id')->count('user_id');
        });

        return view('roles.index', ['roles' => $roles]);
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
