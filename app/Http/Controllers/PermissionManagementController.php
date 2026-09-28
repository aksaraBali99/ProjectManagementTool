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

    /**
     * task #73 phase 1: a permission locked OFF for one specific
     * otherwise-editable role, the same idea as the whole super_admin/
     * owner columns being locked ON, just narrower — Client can never
     * hold view_documents (DocumentPolicy::view() and
     * User::documentOrganizationIds() give a client their document
     * visibility a completely different way, independent of this
     * permission), so its checkbox renders unchecked+disabled for Client
     * specifically, and update() strips it from Client's submitted array
     * unconditionally, even on a direct, tampered POST that includes it.
     */
    private const FORCED_OFF = [
        Role::CLIENT => ['view_documents'],
    ];

    public function edit(): View
    {
        Gate::authorize('viewAny', Role::class);

        $roles = $this->rolesInDisplayOrder();
        $permissionGroups = Permission::orderBy('name')->get()->groupBy('group');

        $grants = [];
        foreach ($roles as $role) {
            $grants[$role->id] = $role->permissions()->pluck('permissions.id')->all();
        }

        $forcedOffPermissionIds = [];
        foreach ($roles as $role) {
            $forcedOffPermissionIds[$role->id] = $this->forcedOffPermissionIdsForRole($role);
        }

        return view('roles.permissions', [
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'grants' => $grants,
            'editableRoleSlugs' => self::EDITABLE_ROLE_SLUGS,
            'forcedOffPermissionIds' => $forcedOffPermissionIds,
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

            // Stripped unconditionally, not just left unrendered in the
            // form — a direct POST naming Client + view_documents must
            // never persist it, regardless of what the request claims.
            $permissionIds = array_values(array_diff($permissionIds, $this->forcedOffPermissionIdsForRole($role)));

            $role->permissions()->sync($permissionIds);
        }

        return redirect()->route('roles.permissions.edit')->with('status', 'Permissions updated.');
    }

    /**
     * @return array<int, int>
     */
    private function forcedOffPermissionIdsForRole(Role $role): array
    {
        $slugs = self::FORCED_OFF[$role->slug] ?? [];

        if (empty($slugs)) {
            return [];
        }

        return Permission::whereIn('slug', $slugs)->pluck('id')->all();
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
