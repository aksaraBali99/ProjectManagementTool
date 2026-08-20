<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserPasswordRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('users.create', [
            'organizations' => Organization::orderBy('name')->get(),
            'assignableRoles' => Role::assignableInCompany()->orderBy('name')->get(),
            'canGrantSuperAdmin' => auth()->user()->isOwner(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

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
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $user->update([
            'username' => $request->string('username'),
            'name' => $request->string('name'),
            'employee_id' => $request->string('employee_id'),
            'email' => $request->string('email'),
            'phone' => $request->string('phone'),
        ]);

        $isTargetOwner = $user->roles()->where('slug', Role::OWNER)->exists();

        if (! $isTargetOwner) {
            $this->syncCompanyRoles($user, $request->input('roles', []));
            $this->syncSuperAdminGrant($user, $request);
        }

        return redirect()->route('users.index')->with('status', 'User updated.');
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $user->update(['password' => $request->string('password')]);

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
        $roleIds = Role::assignableInCompany()->pluck('id', 'slug');

        foreach ($roles as $organizationId => $slug) {
            if ($slug === 'none') {
                OrgMember::where('organization_id', $organizationId)
                    ->where('user_id', $user->id)
                    ->delete();

                continue;
            }

            OrgMember::updateOrCreate(
                ['organization_id' => $organizationId, 'user_id' => $user->id],
                ['role_id' => $roleIds[$slug]],
            );
        }
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
}
