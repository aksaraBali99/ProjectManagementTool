@extends('layouts.authenticated')

@section('title', 'Edit user — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('users.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Users</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Edit user</h1>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-[#E1F5EE] p-3 text-[12px] text-[#085041]">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user) }}" class="mt-6" id="edit-user-form" novalidate>
        @csrf
        @method('PUT')

        @include('users._form', ['isEdit' => true])
    </form>

    <dialog id="password-modal" class="w-full max-w-sm rounded-lg border border-gray-200 p-6 shadow-lg backdrop:bg-black/30">
        <h2 class="text-[14px] font-medium text-[#1F2937]">Change password</h2>

        @if ($errors->has('password'))
            <div class="mt-3 rounded-md bg-red-50 p-3 text-[12px] text-red-700">
                {{ $errors->first('password') }}
            </div>
        @endif

        <form method="POST" action="{{ route('users.password.update', $user) }}" class="mt-4 space-y-4" id="password-form">
            @csrf
            @method('PUT')

            <div>
                <label for="new-password" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">New password</label>
                @include('users._password-input', ['id' => 'new-password', 'name' => 'password'])
            </div>

            <div>
                <label for="new-password-confirmation" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Confirm new password</label>
                @include('users._password-input', ['id' => 'new-password-confirmation', 'name' => 'password_confirmation'])
            </div>

            <p class="text-[11px] text-gray-500">At least 8 characters, with upper &amp; lower case, a number, and a symbol.</p>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('password-modal').close()"
                    class="text-[12px] text-gray-600 hover:underline">
                    Cancel
                </button>
                <button type="submit" id="password-submit-btn" disabled
                    class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56] disabled:cursor-not-allowed disabled:opacity-50">
                    Save password
                </button>
            </div>
        </form>
    </dialog>
</div>

@include('users._inline-validation')

<script>
    (function () {
        const password = document.getElementById('new-password');
        const confirmation = document.getElementById('new-password-confirmation');
        const submitBtn = document.getElementById('password-submit-btn');
        const modal = document.getElementById('password-modal');

        function isStrong(value) {
            return value.length >= 8
                && /[a-z]/.test(value)
                && /[A-Z]/.test(value)
                && /[0-9]/.test(value)
                && /[^A-Za-z0-9]/.test(value);
        }

        function validate() {
            submitBtn.disabled = !(isStrong(password.value) && password.value === confirmation.value && confirmation.value.length > 0);
        }

        password.addEventListener('input', validate);
        confirmation.addEventListener('input', validate);

        @if ($errors->has('password'))
            modal.showModal();
        @endif

        const roleSelects = document.querySelectorAll('.role-select');
        const rolesError = document.getElementById('roles-error');
        const grantSuperAdmin = document.querySelector('input[name="grant_super_admin"]');
        const superAdminSection = document.getElementById('super-admin-section');
        const superAdminError = document.getElementById('grant-super-admin-error');
        const hasSuperAdminServerError = {{ $errors->has('grant_super_admin') ? 'true' : 'false' }};
        const targetAlreadySuperAdmin = {{ ($isTargetSuperAdmin ?? false) ? 'true' : 'false' }};

        function validateRoles(force) {
            if (! roleSelects.length || ! rolesError) return;
            const values = Array.from(roleSelects).map(function (select) { return select.value; });
            const clientCount = values.filter(function (value) { return value === 'client'; }).length;
            const hasAccess = values.some(function (value) { return value !== 'none'; })
                || (grantSuperAdmin && grantSuperAdmin.checked);

            let message = null;
            if (clientCount > 1) {
                message = 'A user can only be assigned the Client role in one company.';
            } else if (clientCount === 1 && values.some(function (value) { return value !== 'none' && value !== 'client'; })) {
                message = 'A user assigned the Client role in one company cannot hold another role in a different company.';
            } else if (! hasAccess && force) {
                message = 'Assign this user a role in at least one company.';
            }

            if (message) {
                rolesError.textContent = message;
                rolesError.style.display = '';
            } else {
                rolesError.style.display = 'none';
            }
        }

        function updateSuperAdminSection(force) {
            if (! superAdminSection) return;

            const hasManagement = Array.from(roleSelects).some(function (select) { return select.value === 'management'; });
            const checked = grantSuperAdmin && grantSuperAdmin.checked;
            const show = hasManagement || checked || targetAlreadySuperAdmin || hasSuperAdminServerError;
            superAdminSection.style.display = show ? '' : 'none';

            if (! superAdminError) return;
            const invalid = checked && ! hasManagement && ! targetAlreadySuperAdmin;
            if (invalid && force) {
                superAdminError.textContent = 'Grant Super Admin requires assigning this user the Management role in at least one company.';
                superAdminError.style.display = '';
            } else if (! invalid) {
                superAdminError.style.display = 'none';
            }
        }

        roleSelects.forEach(function (select) {
            select.addEventListener('change', function () {
                validateRoles(true);
                updateSuperAdminSection(true);
            });
        });

        if (grantSuperAdmin) {
            grantSuperAdmin.addEventListener('change', function () {
                validateRoles(true);
                updateSuperAdminSection(true);
            });
        }

        updateSuperAdminSection(false);

        document.getElementById('edit-user-form').addEventListener('submit', function (event) {
            validateRoles(true);
            updateSuperAdminSection(true);

            if ((rolesError && rolesError.style.display !== 'none') || (superAdminError && superAdminError.style.display !== 'none')) {
                event.preventDefault();
            }
        });
    })();
</script>

{{-- Registered last, so by the time this runs every other submit listener
     above (native required-field checks via users._inline-validation, and
     this page's own roles/super-admin checks) has already shown or hidden
     its error message for THIS submit attempt — this only has to look at
     what's visible now and reveal whichever tab owns the first one, not
     recompute any validation itself. --}}
<script>
    (function () {
        const form = document.getElementById('edit-user-form');
        const root = document.getElementById('user-form-tabs');
        if (! form || ! root || ! root.showTab) return;

        form.addEventListener('submit', function () {
            const target = form.querySelector(':invalid')
                || Array.from(form.querySelectorAll('.field-error, #roles-error, #grant-super-admin-error'))
                    .find(function (el) { return el.style.display !== 'none' && el.textContent.trim() !== ''; });

            if (! target) return;

            const panel = target.closest('[data-tab-panel]');
            if (panel) root.showTab(parseInt(panel.dataset.tabPanel, 10));
        });
    })();
</script>

@include('users._unsaved-changes-guard', ['formId' => 'edit-user-form'])
@endsection
