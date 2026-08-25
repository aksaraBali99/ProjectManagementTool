{{--
    Shared 3-tab wizard for both the Add and Edit user pages — the only
    real difference between the two contexts is the password field (a
    plain input with a strength hint on create; a locked placeholder plus
    a "Change Password" modal on edit), branched below via $isEdit. Every
    other field/section is identical, so it lives here once rather than
    drifting across two copies.

    Tab 3 (Department Assignment) doesn't fetch anything client-side — it
    pre-renders EVERY organization's active departments up front (same
    "roles" dataset Tab 2 already renders one row per organization for),
    and JS only ever toggles which section is visible based on Tab 2's
    live role-select values. The checkboxes themselves are real, always-
    submitting form controls regardless of visibility, so there's no
    separate client-side state to keep in sync with the DOM — the DOM IS
    the state. See the script block at the bottom for the visibility sync
    and the tab-switch-to-first-invalid-field-on-submit behavior.
--}}
@php
    $tab1Fields = ['username', 'password', 'name', 'employee_id', 'email', 'phone'];
    $tab2Fields = ['roles', 'grant_super_admin'];
    $tab3Fields = ['access_permissions'];

    $hasErrorsIn = fn (array $fields) => collect($fields)->contains(
        fn ($field) => $errors->has($field) || $errors->has($field.'.*')
    );

    $initialTab = 1;
    if ($hasErrorsIn($tab2Fields)) {
        $initialTab = 2;
    }
    if ($hasErrorsIn($tab3Fields)) {
        $initialTab = 3;
    }
    if ($hasErrorsIn($tab1Fields)) {
        $initialTab = 1;
    }

    // Unchecked checkboxes never submit, so old('access_permissions') can't
    // distinguish "the admin unchecked everything" from "this key was never
    // sent at all" — old()'s own default-on-missing-key behavior would wrongly
    // fall back to $allowedDepartmentIds (the DB state) for the former. $errors
    // being non-empty is what disambiguates: only trust old() input, exactly
    // as submitted (possibly empty), when this render IS a round-trip from a
    // failed submission; otherwise use the last-saved DB state.
    $checkedDepartmentIds = $errors->any() ? old('access_permissions', []) : $allowedDepartmentIds;
@endphp

<div id="user-form-tabs" data-initial-tab="{{ $initialTab }}">
    <div class="flex border-b border-gray-200" role="tablist">
        <button type="button" class="user-tab-header {{ $initialTab === 1 ? 'user-tab-header--active' : '' }} px-4 py-2 text-[12px] font-medium" data-tab="1">User Details</button>
        <button type="button" class="user-tab-header {{ $initialTab === 2 ? 'user-tab-header--active' : '' }} px-4 py-2 text-[12px] font-medium" data-tab="2">Company Role Assignment</button>
        <button type="button" class="user-tab-header {{ $initialTab === 3 ? 'user-tab-header--active' : '' }} px-4 py-2 text-[12px] font-medium" data-tab="3">Department Assignment</button>
    </div>

    {{-- Tab 1: User Details --}}
    <div data-tab-panel="1" class="mt-4 space-y-4" style="{{ $initialTab === 1 ? '' : 'display: none;' }}">
        <div>
            <label for="username" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Username <span class="text-red-600">*</span></label>
            <input id="username" name="username" type="text" value="{{ old('username', $isEdit ? $user->username : '') }}" required
                @if (! $isEdit) autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" @endif
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            @error('username')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @if ($isEdit)
            <div>
                <label class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Password</label>
                <div class="mt-1 flex items-center gap-3">
                    <input type="password" value="********" disabled
                        class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-[12px] text-gray-400">
                    <button type="button" onclick="document.getElementById('password-modal').showModal()"
                        class="whitespace-nowrap rounded-md border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                        Change Password
                    </button>
                </div>
            </div>
        @else
            <div>
                <label for="password" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Password <span class="text-red-600">*</span></label>
                @include('users._password-input', ['id' => 'password', 'name' => 'password', 'autocomplete' => 'new-password'])
                <p id="password-hint" class="mt-1 text-[11px] text-gray-500">At least 8 characters, with upper &amp; lower case, a number, and a symbol.</p>
                @error('password')
                    <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Full name <span class="text-red-600">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $isEdit ? $user->name : '') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            @error('name')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="employee_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Employee ID <span class="text-red-600">*</span></label>
            <input id="employee_id" name="employee_id" type="text" value="{{ old('employee_id', $isEdit ? $user->employee_id : ($suggestedEmployeeId ?? '')) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            @error('employee_id')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Email <span class="text-red-600">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email', $isEdit ? $user->email : '') }}" required
                pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                title="Must include a domain with an extension, e.g. name@example.com"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
            @error('email')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @include('users._phone-input', ['phoneValue' => old('phone', $isEdit ? $user->phone : '')])

        @if ($isEdit)
            <div>
                <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Avatar</span>
                <div class="mt-2 flex items-center gap-2">
                    <x-avatar :user="$user" size="32px" />
                    <span class="text-[11px] text-gray-500">Generated automatically from the saved name.</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Tab 2: Company Role Assignment --}}
    <div data-tab-panel="2" class="mt-4 space-y-4" style="{{ $initialTab === 2 ? '' : 'display: none;' }}">
        @if ($isTargetOwner)
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-3">
                <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Global role</span>
                <p class="mt-1 text-[12px] font-medium text-[#1F2937]">{{ $globalRoles->implode(', ') }}</p>
                <p class="mt-1 text-[11px] text-gray-500">
                    This user has full cross-company access and doesn't need a per-company role. Global roles aren't
                    assignable from this page.
                </p>
            </div>
        @else
            <div>
                <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Company roles <span class="text-red-600">*</span></span>
                <p class="mt-1 text-[11px] text-gray-500">Assign this user a role in at least one company.</p>
                <div class="mt-2 space-y-2">
                    @foreach ($organizations as $organization)
                        @php $current = old('roles.'.$organization->id, $currentRoles[$organization->id] ?? 'none') @endphp
                        <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                            <span class="text-[12px] font-medium text-[#1F2937]">{{ $organization->name }}</span>
                            <select name="roles[{{ $organization->id }}]" data-org-id="{{ $organization->id }}"
                                class="role-select rounded-md border border-gray-300 px-2 py-1 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                                <option value="none" {{ $current === 'none' ? 'selected' : '' }}>No access</option>
                                @foreach ($assignableRoles as $role)
                                    <option value="{{ $role->slug }}" {{ $current === $role->slug ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
                <p id="roles-error" class="field-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
                @error('roles')
                    <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if ($canGrantSuperAdmin)
                <div id="super-admin-section" class="rounded-md border border-gray-200 px-3 py-3">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="grant_super_admin" value="1" {{ old('grant_super_admin', $isTargetSuperAdmin) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-brand-600 focus:ring-brand-600">
                        <span class="text-[12px] font-medium text-[#1F2937]">Grant Super Admin</span>
                    </label>
                    <p class="mt-1 text-[11px] text-gray-500">
                        Full access to every company, everything — bypasses per-company roles entirely. Requires
                        assigning this user the Management role in at least one company. Only an Owner can grant this.
                    </p>
                    <p id="grant-super-admin-error" class="field-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
                    @error('grant_super_admin')
                        <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif
        @endif
    </div>

    {{-- Tab 3: Department Assignment — scoped to this user only, live off Tab 2 --}}
    <div data-tab-panel="3" class="mt-4 space-y-4" style="{{ $initialTab === 3 ? '' : 'display: none;' }}">
        @if ($isTargetOwner)
            <p class="text-[12px] text-gray-500">This user has full cross-company access and doesn't need department-level access.</p>
        @else
            <p class="text-[11px] text-gray-500">
                Departments this user can see, per company where they hold the Staff role. Set a company's role to
                Staff in the Company Role Assignment tab to configure its departments here.
            </p>

            <div id="department-sections" class="mt-2 space-y-4">
                @foreach ($organizations as $organization)
                    @php
                        $departments = $departmentsByOrganization[$organization->id] ?? collect();
                        $currentRole = old('roles.'.$organization->id, $currentRoles[$organization->id] ?? 'none');
                    @endphp
                    <div class="department-org-section" data-org-id="{{ $organization->id }}" style="{{ $currentRole === 'staff' ? '' : 'display: none;' }}">
                        <h3 class="text-[11px] font-semibold uppercase tracking-[0.05em] text-gray-500">{{ $organization->name }}</h3>
                        @if ($departments->isEmpty())
                            <p class="mt-1 text-[11px] text-gray-500">{{ $organization->name }} has no active departments yet.</p>
                        @else
                            <div class="mt-2 space-y-2">
                                @foreach ($departments as $department)
                                    <label class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                                        <span class="inline-flex items-center gap-2 text-[12px] font-medium text-[#1F2937]">
                                            <span class="inline-block h-2 w-2 rounded-full" style="background-color: {{ $department->color }}"></span>
                                            {{ $department->name }}
                                        </span>
                                        <input type="checkbox" name="access_permissions[]" value="{{ $department->id }}"
                                            class="rounded border-gray-300 text-brand-600 focus:ring-brand-600"
                                            {{ in_array($department->id, $checkedDepartmentIds) ? 'checked' : '' }}>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <p id="no-staff-companies-message" class="text-[12px] text-gray-500" style="display: none;">
                Assign this user the Staff role in a company (Company Role Assignment tab) to configure department access.
            </p>
            @error('access_permissions')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
            @error('access_permissions.*')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        @endif
    </div>

    <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
        <div class="flex items-center gap-2">
            <button type="button" id="tab-prev-btn"
                class="rounded-md border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">
                Previous
            </button>
            <button type="button" id="tab-next-btn"
                class="rounded-md border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50">
                Next
            </button>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" id="submit-btn"
                class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                {{ $isEdit ? 'Save changes' : 'Create user' }}
            </button>
            <a href="{{ route('users.index') }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </div>
</div>

<style>
    .user-tab-header { color: #6B7280; border-bottom: 2px solid transparent; }
    .user-tab-header:hover { color: #374151; }
    .user-tab-header--active { color: #1D9E75; border-bottom-color: #1D9E75; }
</style>

<script>
    (function () {
        const root = document.getElementById('user-form-tabs');
        if (! root) return;

        const panels = Array.prototype.slice.call(root.querySelectorAll('[data-tab-panel]'));
        const headers = Array.prototype.slice.call(root.querySelectorAll('.user-tab-header'));
        const prevBtn = document.getElementById('tab-prev-btn');
        const nextBtn = document.getElementById('tab-next-btn');
        const tabCount = panels.length;
        let activeTab = parseInt(root.dataset.initialTab, 10) || 1;

        const roleSelects = Array.prototype.slice.call(root.querySelectorAll('.role-select'));
        const noStaffMessage = document.getElementById('no-staff-companies-message');

        // Live dependency: Tab 3 only shows a company's departments while
        // that company's role-select (Tab 2, possibly unsaved) currently
        // reads Staff. Reading select.value directly means this always
        // reflects in-browser form state, never the last-saved database
        // state — runs on every role change AND whenever Tab 3 becomes
        // active, so it's never stale regardless of which tab was visited
        // last.
        function syncDepartmentVisibility() {
            let anyVisible = false;

            roleSelects.forEach(function (select) {
                const section = root.querySelector('.department-org-section[data-org-id="' + select.dataset.orgId + '"]');
                if (! section) return;

                const isStaff = select.value === 'staff';
                section.style.display = isStaff ? '' : 'none';
                if (isStaff) anyVisible = true;
            });

            if (noStaffMessage) {
                noStaffMessage.style.display = (roleSelects.length && ! anyVisible) ? '' : 'none';
            }
        }

        roleSelects.forEach(function (select) {
            select.addEventListener('change', syncDepartmentVisibility);
        });

        function showTab(tab) {
            activeTab = tab;

            panels.forEach(function (panel) {
                panel.style.display = String(panel.dataset.tabPanel) === String(tab) ? '' : 'none';
            });

            headers.forEach(function (header) {
                header.classList.toggle('user-tab-header--active', String(header.dataset.tab) === String(tab));
            });

            if (prevBtn) prevBtn.disabled = tab === 1;
            if (nextBtn) nextBtn.disabled = tab === tabCount;

            if (tab === 3) syncDepartmentVisibility();
        }

        headers.forEach(function (header) {
            header.addEventListener('click', function () {
                showTab(parseInt(header.dataset.tab, 10));
            });
        });

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                if (activeTab > 1) showTab(activeTab - 1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                if (activeTab < tabCount) showTab(activeTab + 1);
            });
        }

        syncDepartmentVisibility();
        showTab(activeTab);

        // Exposed for a script registered AFTER this one (users._inline-
        // validation's generic required-field check, and each page's own
        // roles/super-admin cross-field checks) to call once THEY'VE
        // populated their error displays — see the "reveal the tab with
        // the first validation error" script at the bottom of
        // users/create.blade.php and users/edit.blade.php. Doing the
        // reveal there, last, rather than here, first, is deliberate: at
        // the moment THIS script runs on submit, custom errors like
        // #roles-error haven't been computed/shown yet for this attempt,
        // so scanning for them here would only ever see last submit's
        // stale state.
        root.showTab = showTab;
    })();
</script>
