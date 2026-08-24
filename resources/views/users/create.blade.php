@extends('layouts.authenticated')

@section('title', 'Add user — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('users.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Users</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Add user</h1>

    <form method="POST" action="{{ route('users.store') }}" class="mt-6" id="create-user-form" novalidate>
        @csrf

        @include('users._form', ['isEdit' => false])
    </form>
</div>

@include('users._inline-validation')

<script>
    (function () {
        const password = document.getElementById('password');
        const passwordHint = document.getElementById('password-hint');

        function isStrong(value) {
            return value.length >= 8
                && /[a-z]/.test(value)
                && /[A-Z]/.test(value)
                && /[0-9]/.test(value)
                && /[^A-Za-z0-9]/.test(value);
        }

        function validatePassword(showIfInvalid) {
            const strong = isStrong(password.value);
            if (strong) {
                passwordHint.classList.remove('text-red-600');
                passwordHint.classList.add('text-gray-500');
            } else if (showIfInvalid) {
                passwordHint.classList.remove('text-gray-500');
                passwordHint.classList.add('text-red-600');
            }
        }

        password.addEventListener('blur', function () { validatePassword(true); });
        password.addEventListener('input', function () {
            validatePassword(passwordHint.classList.contains('text-red-600'));
        });

        const roleSelects = document.querySelectorAll('.role-select');
        const rolesError = document.getElementById('roles-error');
        const grantSuperAdmin = document.querySelector('input[name="grant_super_admin"]');
        const superAdminSection = document.getElementById('super-admin-section');
        const superAdminError = document.getElementById('grant-super-admin-error');
        const hasSuperAdminServerError = {{ $errors->has('grant_super_admin') ? 'true' : 'false' }};

        function validateRoles(force) {
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
            const show = hasManagement || checked || hasSuperAdminServerError;
            superAdminSection.style.display = show ? '' : 'none';

            if (! superAdminError) return;
            const invalid = checked && ! hasManagement;
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

        document.getElementById('create-user-form').addEventListener('submit', function (event) {
            validatePassword(true);
            validateRoles(true);
            updateSuperAdminSection(true);

            if (! isStrong(password.value) || rolesError.style.display !== 'none' || (superAdminError && superAdminError.style.display !== 'none')) {
                event.preventDefault();
            }
        });
    })();
</script>

{{-- Registered last, so by the time this runs every other submit listener
     above (native required-field checks via users._inline-validation, and
     this page's own password/roles/super-admin checks) has already shown
     or hidden its error message for THIS submit attempt — this only has
     to look at what's visible now and reveal whichever tab owns the
     first one, not recompute any validation itself. --}}
<script>
    (function () {
        const form = document.getElementById('create-user-form');
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

@include('users._unsaved-changes-guard', ['formId' => 'create-user-form'])
@endsection
