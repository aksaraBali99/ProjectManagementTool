<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserPasswordRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\Organization;
use App\Models\OrgMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $users = User::query()
            ->with('orgMemberships.organization', 'orgMemberships.role', 'roles', 'emails')
            ->orderBy('name')
            ->get();

        return view('users.index', ['users' => $users]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('users.create', ['organizations' => Organization::orderBy('name')->get()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $user = User::create([
            'username' => $request->string('username'),
            'password' => $request->string('password'),
            'name' => $request->string('name'),
            'employee_id' => $request->string('employee_id'),
            'is_active' => true,
        ]);

        $this->syncContactRows($user->emails(), $request->input('emails', []), 'email', 'Email');
        $this->syncContactRows($user->phones(), $request->input('phones', []), 'phone', 'Phone number');
        $this->syncCompanyRoles($user, $request->input('roles', []));

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

        return view('users.edit', [
            'user' => $user,
            'organizations' => $organizations,
            'currentRoles' => $currentRoles,
            'globalRoles' => $user->roles()->whereIn('slug', [Role::SUPER_ADMIN, Role::OWNER])->pluck('name'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $user->update([
            'username' => $request->string('username'),
            'name' => $request->string('name'),
            'employee_id' => $request->string('employee_id'),
        ]);

        $this->syncContactRows($user->emails(), $request->input('emails', []), 'email', 'Email');
        $this->syncContactRows($user->phones(), $request->input('phones', []), 'phone', 'Phone number');

        if (! $user->hasGlobalRole()) {
            $this->syncCompanyRoles($user, $request->input('roles', []));
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
     * @param  array<int|string, string>  $roles  organization_id => 'none'|'staff'|'management'
     */
    private function syncCompanyRoles(User $user, array $roles): void
    {
        $roleIds = Role::whereIn('slug', [Role::STAFF, Role::MANAGEMENT])
            ->pluck('id', 'slug');

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
     * Fully replaces a user's emails or phones with the submitted rows. A
     * blank caption gets a default label ("Email 1", "Phone number 2", ...)
     * that increments per base label within this save.
     *
     * @param  HasMany<Model, User>  $relation
     * @param  array<int, array{label?: string, value?: string}>  $rows
     */
    private function syncContactRows(HasMany $relation, array $rows, string $valueField, string $defaultBase): void
    {
        $relation->delete();

        $assignedLabels = collect();

        foreach ($rows as $row) {
            $label = trim((string) ($row['label'] ?? ''));

            if ($label === '') {
                $label = $this->nextDefaultLabel($assignedLabels, $defaultBase);
            }

            $assignedLabels->push($label);

            $relation->create([$valueField => $row['value'], 'label' => $label]);
        }
    }

    private function nextDefaultLabel(Collection $existingLabels, string $base): string
    {
        $pattern = '/^'.preg_quote($base, '/').' (\d+)$/';

        $max = $existingLabels->reduce(function (int $carry, string $label) use ($pattern) {
            return preg_match($pattern, $label, $matches) ? max($carry, (int) $matches[1]) : $carry;
        }, 0);

        return $base.' '.($max + 1);
    }
}
