@extends('layouts.authenticated')

@section('title', 'Add user — FounderOS')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('users.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Users</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Add user</h1>

    <form method="POST" action="{{ route('users.store') }}" class="mt-6 space-y-4" id="create-user-form" novalidate>
        @csrf

        <div>
            <label for="username" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Username <span class="text-red-600">*</span></label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required
                autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('username')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Password <span class="text-red-600">*</span></label>
            <input id="password" name="password" type="password" required
                autocomplete="new-password"
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            <p id="password-hint" class="mt-1 text-[11px] text-gray-500">At least 8 characters, with upper &amp; lower case, a number, and a symbol.</p>
            @error('password')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Full name <span class="text-red-600">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('name')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="employee_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Employee ID <span class="text-red-600">*</span></label>
            <input id="employee_id" name="employee_id" type="text" value="{{ old('employee_id') }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('employee_id')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Email <span class="text-red-600">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                title="Must include a domain with an extension, e.g. name@example.com"
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('email')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Phone <span class="text-red-600">*</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" required
                pattern="^\+?[0-9\s\-\(\)]{7,20}$"
                title="7–15 digits, may include +, spaces, hyphens, and parentheses"
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('phone')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Company roles <span class="text-red-600">*</span></span>
            <p class="mt-1 text-[11px] text-gray-500">Assign this user a role in at least one company.</p>
            <div class="mt-2 space-y-2">
                @foreach ($organizations as $organization)
                    <div class="flex items-center justify-between rounded-[8px] border border-gray-200 px-3 py-2">
                        <span class="text-[12px] font-medium text-[#1F2937]">{{ $organization->name }}</span>
                        <select name="roles[{{ $organization->id }}]" class="role-select rounded-[8px] border border-gray-300 px-2 py-1 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                            <option value="none" {{ old('roles.'.$organization->id, 'none') === 'none' ? 'selected' : '' }}>No access</option>
                            <option value="staff" {{ old('roles.'.$organization->id) === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="management" {{ old('roles.'.$organization->id) === 'management' ? 'selected' : '' }}>Management</option>
                        </select>
                    </div>
                @endforeach
            </div>
            <p id="roles-error" class="field-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
            @error('roles')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" id="submit-btn"
                class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                Create user
            </button>
            <a href="{{ route('users.index') }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
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

        function validateRoles(force) {
            const hasAccess = Array.from(roleSelects).some(function (select) { return select.value !== 'none'; });
            if (! hasAccess && force) {
                rolesError.textContent = 'Assign this user a role in at least one company.';
                rolesError.style.display = '';
            } else if (hasAccess) {
                rolesError.style.display = 'none';
            }
        }

        roleSelects.forEach(function (select) {
            select.addEventListener('change', function () { validateRoles(true); });
        });

        document.getElementById('create-user-form').addEventListener('submit', function (event) {
            validatePassword(true);
            validateRoles(true);

            if (! isStrong(password.value) || rolesError.style.display !== 'none') {
                event.preventDefault();
            }
        });
    })();
</script>

@include('users._unsaved-changes-guard', ['formId' => 'create-user-form'])
@endsection
