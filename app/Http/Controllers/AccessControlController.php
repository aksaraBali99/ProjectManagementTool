<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentOrganization;
use App\Models\AccessPermission;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AccessControlController extends Controller
{
    use ResolvesCurrentOrganization;

    public function index(?Organization $organization = null): View|RedirectResponse
    {
        Gate::authorize('access-control.view');

        $organizations = Organization::where('is_active', true)->orderBy('name')->get();

        if ($organizations->isEmpty()) {
            return view('access-control.index', [
                'organizations' => $organizations,
                'organization' => null,
                'departments' => collect(),
                'staffUsers' => collect(),
                'grid' => [],
            ]);
        }

        $organization = $this->resolveCurrentOrganization($organizations, $organization);

        $departments = $organization->departments()->where('is_active', true)->orderBy('name')->get();

        $staffUsers = User::where('is_active', true)
            ->whereHas('orgMemberships.role', fn ($query) => $query->where('slug', Role::STAFF))
            ->distinct()
            ->orderBy('name')
            ->get();

        $permissions = AccessPermission::where('organization_id', $organization->id)->get();

        $grid = [];
        foreach ($staffUsers as $staff) {
            foreach ($departments as $department) {
                $permission = $permissions->first(
                    fn (AccessPermission $p) => $p->user_id === $staff->id && $p->department_id === $department->id
                );
                $grid[$staff->id][$department->id] = (bool) ($permission?->allowed);
            }
        }

        return view('access-control.index', [
            'organizations' => $organizations,
            'organization' => $organization,
            'departments' => $departments,
            'staffUsers' => $staffUsers,
            'grid' => $grid,
        ]);
    }

    public function toggle(Request $request): RedirectResponse
    {
        Gate::authorize('access-control.view');

        $data = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'allowed' => ['required', 'boolean'],
        ]);

        $permission = AccessPermission::updateOrCreate(
            [
                'user_id' => $data['user_id'],
                'organization_id' => $data['organization_id'],
                'department_id' => $data['department_id'],
            ],
            ['allowed' => $data['allowed']],
        );

        if ($permission->allowed) {
            $staffRoleId = Role::where('slug', Role::STAFF)->value('id');

            OrgMember::firstOrCreate(
                ['organization_id' => $data['organization_id'], 'user_id' => $data['user_id']],
                ['role_id' => $staffRoleId],
            );
        }

        return redirect()->route('access-control.index', $data['organization_id'])->with('status', 'Access updated.');
    }
}
