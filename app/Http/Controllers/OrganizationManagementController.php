<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organizations\StoreOrganizationRequest;
use App\Http\Requests\Organizations\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrganizationManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Organization::class);

        return view('organizations.index', [
            'organizations' => Organization::withCount('departments', 'projects')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Organization::class);

        return view('organizations.create');
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        Gate::authorize('create', Organization::class);

        Organization::create($request->only('name', 'slug', 'accent_color'));

        return redirect()->route('organizations.index')->with('status', 'Company created.');
    }

    public function edit(Organization $organization): View
    {
        Gate::authorize('update', $organization);

        return view('organizations.edit', ['organization' => $organization]);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('update', $organization);

        $organization->update($request->only('name', 'slug', 'accent_color'));

        return redirect()->route('organizations.index')->with('status', 'Company updated.');
    }

    public function destroy(Request $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('delete', $organization);

        if ($request->string('confirm_name')->toString() !== $organization->name) {
            return back()->withErrors(['confirm_name' => 'Type the company name exactly to confirm deletion.']);
        }

        $organization->delete();

        return redirect()->route('organizations.index')->with('status', 'Company deleted.');
    }
}
