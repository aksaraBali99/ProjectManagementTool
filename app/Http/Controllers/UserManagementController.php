<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserPasswordRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\AccessPermission;
use App\Models\Department;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use App\Services\CompanyRoleSyncer;
use App\Services\Import\EmployeeIdGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with('orgMemberships.organization', 'orgMemberships.role', 'roles')
            ->orderBy('name')
            ->get();

        return view('users.index', ['users' => $users]);
    }

    public function create(EmployeeIdGenerator $employeeIdGenerator): View
    {
        Gate::authorize('create', User::class);

        $organizations = Organization::orderBy('name')->get();

        return view('users.create', [
            'organizations' => $organizations,
            'assignableRoles' => Role::assignableInCompany()->orderBy('name')->get(),
            'canGrantSuperAdmin' => auth()->user()->isOwner(),
            'currentRoles' => collect(),
            'isTargetOwner' => false,
            'isTargetSuperAdmin' => false,
            'departmentsByOrganization' => $this->departmentsByOrganization($organizations),
            'allowedDepartmentIds' => [],
            'suggestedEmployeeId' => $employeeIdGenerator->next('employee'),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        // User fields, org_members rows, and access_permissions rows are
        // one save from the admin's point of view (three tabs of the same
        // form) — either all of it lands, or none of it does.
        DB::transaction(function () use ($request) {
            $user = User::create([
                'username' => $request->string('username'),
                'password' => $request->string('password'),
                'name' => $request->string('name'),
                'employee_id' => $request->string('employee_id'),
                'email' => $request->string('email'),
                'phone' => $request->string('phone'),
                'is_active' => true,
            ]);

            $this->syncCompanyRoles($user, $request->input('roles', []));
            $this->syncSuperAdminGrant($user, $request);
            $this->syncDepartmentAccess($user, $request->input('roles', []), $request->input('access_permissions', []));
        });

        return redirect()->route('users.index')->with('status', 'User created.');
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        $organizations = Organization::orderBy('name')->get();

        $currentRoles = $user->orgMemberships()
            ->with('role')
            ->get()
            ->mapWithKeys(fn (OrgMember $membership) => [$membership->organization_id => $membership->role->slug]);

        // Owner itself stays fully locked (unmanageable via this form); a
        // target holding only Super Admin, or no global role, still gets
        // the editable Company roles section — and, for an Owner viewer,
        // the Grant Super Admin toggle.
        $isTargetOwner = $user->roles()->where('slug', Role::OWNER)->exists();

        return view('users.edit', [
            'user' => $user,
            'organizations' => $organizations,
            'assignableRoles' => Role::assignableInCompany()->orderBy('name')->get(),
            'currentRoles' => $currentRoles,
            'isTargetOwner' => $isTargetOwner,
            'isTargetSuperAdmin' => $user->roles()->where('slug', Role::SUPER_ADMIN)->exists(),
            'canGrantSuperAdmin' => auth()->user()->isOwner(),
            'globalRoles' => $user->roles()->whereIn('slug', Role::GLOBAL_SLUGS)->pluck('name'),
            'departmentsByOrganization' => $this->departmentsByOrganization($organizations),
            'allowedDepartmentIds' => $user->accessPermissions()->where('allowed', true)->pluck('department_id')->all(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $isTargetOwner = $user->roles()->where('slug', Role::OWNER)->exists();

        DB::transaction(function () use ($request, $user, $isTargetOwner) {
            $user->update([
                'username' => $request->string('username'),
                'name' => $request->string('name'),
                'employee_id' => $request->string('employee_id'),
                'email' => $request->string('email'),
                'phone' => $request->string('phone'),
            ]);

            if (! $isTargetOwner) {
                $this->syncCompanyRoles($user, $request->input('roles', []));
                $this->syncSuperAdminGrant($user, $request);
                $this->syncDepartmentAccess($user, $request->input('roles', []), $request->input('access_permissions', []));
            }
        });

        return redirect()->route('users.index')->with('status', 'User updated.');
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $user->update(['password' => $request->string('password'), 'must_change_password' => false]);

        return redirect()->route('users.edit', $user)->with('status', 'Password changed.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $user->update(['is_active' => ! $user->is_active]);

        return redirect()->route('users.index')->with('status', $user->is_active ? 'User activated.' : 'User deactivated.');
    }

    /**
     * @param  array<int|string, string>  $roles  organization_id => 'none' or a company-assignable role slug
     */
    private function syncCompanyRoles(User $user, array $roles): void
    {
        app(CompanyRoleSyncer::class)->sync($user, $roles);
    }

    /**
     * Only an Owner can grant or revoke Super Admin, and only via this
     * explicit toggle — checked/unchecked regardless of who's submitting,
     * since a tampered request from a non-Owner must still be ignored here
     * even if it somehow passed validation.
     */
    private function syncSuperAdminGrant(User $user, StoreUserRequest|UpdateUserRequest $request): void
    {
        if (! auth()->user()->isOwner()) {
            return;
        }

        $superAdminId = Role::where('slug', Role::SUPER_ADMIN)->value('id');

        if ($request->boolean('grant_super_admin')) {
            $user->roles()->syncWithoutDetaching([$superAdminId]);
        } else {
            $user->roles()->detach($superAdminId);
        }
    }

    /**
     * Full replace of this user's access_permissions rows on every save,
     * scoped to companies the JUST-SUBMITTED roles mark as staff — a role
     * change away from staff in the same save silently drops that
     * company's department grants rather than leaving orphaned rows a
     * later staff reassignment would resurrect. Submitted department IDs
     * for a non-staff company are ignored rather than rejected, the same
     * defense-in-depth pattern as CommentController::syncMentions()
     * filtering against a server-derived eligible set instead of trusting
     * the client's tab-visibility logic.
     *
     * @param  array<int|string, string>  $roles  organization_id => 'none' or a company-assignable role slug
     * @param  array<int, int|string>  $departmentIds
     */
    private function syncDepartmentAccess(User $user, array $roles, array $departmentIds): void
    {
        AccessPermission::where('user_id', $user->id)->delete();

        $staffOrgIds = collect($roles)
            ->filter(fn ($role) => $role === Role::STAFF)
            ->keys()
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($staffOrgIds) || empty($departmentIds)) {
            return;
        }

        $departments = Department::whereIn('id', $departmentIds)
            ->whereIn('organization_id', $staffOrgIds)
            ->where('is_active', true)
            ->get(['id', 'organization_id']);

        foreach ($departments as $department) {
            AccessPermission::create([
                'user_id' => $user->id,
                'organization_id' => $department->organization_id,
                'department_id' => $department->id,
                'allowed' => true,
            ]);
        }
    }

    /**
     * @return array<int, Collection<int, array{id: int, name: string, color: string}>>
     */
    private function departmentsByOrganization(Collection $organizations): array
    {
        return $organizations->mapWithKeys(fn (Organization $organization) => [
            $organization->id => $organization->departments()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'color']),
        ])->all();
    }
}
