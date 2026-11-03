<?php

namespace App\Http\Controllers;

use App\Http\Requests\Organizations\StoreOrganizationRequest;
use App\Http\Requests\Organizations\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Support\CompanyCode;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
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

        $this->savingCode($request->validated('code'), null, fn () => Organization::create([
            'name' => $request->string('name'),
            'slug' => Organization::generateUniqueSlug($request->string('name')),
            'code' => $request->validated('code'),
            'accent_color' => $request->string('accent_color'),
        ]));

        return redirect()->route('organizations.index')->with('status', 'Company created.');
    }

    /**
     * task #118 phase 2: GET organizations.code-suggestion?name=...[&except=id]
     * — the Add/Edit Company form's suggested task code, from
     * CompanyCode::suggest() (the rule lives only there, not in JS)
     * against every company's code, deactivated ones included. except=
     * leaves the company being edited out, so its own code doesn't count
     * as taken. Never fails on an odd name: one with no letters gets XXX
     * or the next free code.
     */
    public function codeSuggestion(Request $request): JsonResponse
    {
        Gate::authorize('create', Organization::class);

        $except = $request->integer('except');
        $taken = Organization::query()
            ->when($except > 0, fn ($query) => $query->whereKeyNot($except))
            ->pluck('code');

        return response()->json(['code' => CompanyCode::suggest((string) $request->query('name', ''), $taken)]);
    }

    public function edit(Organization $organization): View
    {
        Gate::authorize('update', $organization);

        return view('organizations.edit', ['organization' => $organization]);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        Gate::authorize('update', $organization);

        // slug is intentionally excluded — fixed once created, never
        // regenerated on rename (see Organization::generateUniqueSlug).
        // So is the task code once the company has issued task numbers
        // (task #118 phase 2): renaming a company never changes its code.
        $attributes = $request->only('name', 'accent_color', 'completed_window_days');
        $code = $organization->hasIssuedTaskNumbers() ? null : $request->validated('code');

        DB::transaction(function () use ($organization, $attributes, $code) {
            if ($code !== null && $code !== $organization->code) {
                // Written only where the company still hasn't issued a
                // task number, in the same statement: the lock was checked
                // when this request began, and a first task created since
                // (BGS-0001) must not be renamed by this save.
                $changed = $this->savingCode($code, $organization, fn () => Organization::whereKey($organization->id)
                    ->where('next_task_number', 1)
                    ->update(['code' => $code, 'updated_at' => now()]));

                if ($changed === 0) {
                    throw ValidationException::withMessages(['code' => UpdateOrganizationRequest::TASK_CODE_LOCKED_MESSAGE]);
                }
            }

            $organization->update($attributes);
        });

        return redirect()->route('organizations.index')->with('status', 'Company updated.');
    }

    /**
     * Runs $save, turning the unique index's rejection of the code (two
     * owners saving the same code at the same moment, after both passed
     * validation) into the form's own "BVA is already used by ..." error
     * instead of a 500.
     */
    private function savingCode(?string $code, ?Organization $organization, callable $save): mixed
    {
        try {
            return $save();
        } catch (UniqueConstraintViolationException $e) {
            $message = $code === null ? null : StoreOrganizationRequest::taskCodeTakenMessage($code, $organization);

            // Not the code after all (no other company holds it): let the
            // real error through.
            if ($message === null) {
                throw $e;
            }

            throw ValidationException::withMessages(['code' => $message]);
        }
    }

    public function toggleActive(Organization $organization): RedirectResponse
    {
        Gate::authorize('update', $organization);

        $organization->update(['is_active' => ! $organization->is_active]);

        return redirect()->route('organizations.index')
            ->with('status', $organization->is_active ? 'Company activated.' : 'Company deactivated.');
    }
}
