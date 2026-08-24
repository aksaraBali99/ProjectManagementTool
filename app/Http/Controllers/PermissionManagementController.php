<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PermissionManagementController extends Controller
{
    /**
     * super_admin and owner are locked in the matrix UI — every checkbox in
     * those two columns renders checked and disabled, so browsers never
     * submit anything for them. update() enforces the same rule
     * server-side too: it only ever processes these three role slugs,
     * regardless of what a direct form submission attempts to include.
     */
    private const EDITABLE_ROLE_SLUGS = [Role::MANAGEMENT, Role::STAFF, Role::CLIENT];

    public function edit(): View
    {
        Gate::authorize('viewAny', Role::class);

        $roles = $this->rolesInDisplayOrder();
        $permissionGroups = Permission::orderBy('name')->get()->groupBy('group');

        $grants = [];
        foreach ($roles as $role) {
            $grants[$role->id] = $role->permissions()->pluck('permissions.id')->all();
        }

        return view('roles.permissions', [
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'grants' => $grants,
            'editableRoleSlugs' => self::EDITABLE_ROLE_SLUGS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        // viewAny, not update — this edits ALL roles' permissions at once,
        // not one specific Role instance, so RolePolicy::update (which
        // requires a Role instance as its second argument) doesn't apply.
        Gate::authorize('viewAny', Role::class);

        $editableRoles = Role::whereIn('slug', self::EDITABLE_ROLE_SLUGS)->get();
        $submitted = $request->input('role_permissions', []);

        foreach ($editableRoles as $role) {
            $permissionIds = collect($submitted[$role->id] ?? [])
                ->map(fn ($id) => (int) $id)
                ->all();

            $role->permissions()->sync($permissionIds);
        }

        return redirect()->route('roles.permissions.edit')->with('status', 'Permissions updated.');
    }

    /**
     * @return Collection<int, Role>
     */
    private function rolesInDisplayOrder()
    {
        $order = [Role::SUPER_ADMIN, Role::OWNER, Role::MANAGEMENT, Role::STAFF, Role::CLIENT];

        return Role::whereIn('slug', $order)->get()
            ->sortBy(fn (Role $role) => array_search($role->slug, $order))
            ->values();
    }
}
