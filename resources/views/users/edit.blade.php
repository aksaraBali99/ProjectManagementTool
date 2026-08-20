@extends('layouts.authenticated')

@section('title', 'Edit user — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('users.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Users</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Edit user</h1>

    @if (session('status'))
        <div class="mt-4 rounded-[8px] bg-[#E1F5EE] p-3 text-[12px] text-[#085041]">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user) }}" class="mt-6 space-y-4" id="edit-user-form" novalidate>
        @csrf
        @method('PUT')

        <div>
            <label for="username" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Username <span class="text-red-600">*</span></label>
            <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('username')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Password</label>
            <div class="mt-1 flex items-center gap-3">
                <input type="password" value="********" disabled
                    class="block w-full rounded-[8px] border border-gray-300 bg-gray-50 px-3 py-2 text-[12px] text-gray-400">
                <button type="button" onclick="document.getElementById('password-modal').showModal()"
                    class="whitespace-nowrap rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] font-medium text-gray-700 hover:bg-gray-50">
                    Change Password
                </button>
            </div>
        </div>

        <div>
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Full name <span class="text-red-600">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('name')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="employee_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Employee ID <span class="text-red-600">*</span></label>
            <input id="employee_id" name="employee_id" type="text" value="{{ old('employee_id', $user->employee_id) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('employee_id')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Email <span class="text-red-600">*</span></label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                title="Must include a domain with an extension, e.g. name@example.com"
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('email')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @include('users._phone-input', ['phoneValue' => old('phone', $user->phone)])

        @if ($isTargetOwner)
            <div class="rounded-[8px] border border-gray-200 bg-gray-50 px-3 py-3">
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
                        <div class="flex items-center justify-between rounded-[8px] border border-gray-200 px-3 py-2">
                            <span class="text-[12px] font-medium text-[#1F2937]">{{ $organization->name }}</span>
                            <select name="roles[{{ $organization->id }}]" class="role-select rounded-[8px] border border-gray-300 px-2 py-1 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                                <option value="none" {{ $current === 'none' ? 'selected' : '' }}>No access</option>
                                <option value="staff" {{ $current === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="management" {{ $current === 'management' ? 'selected' : '' }}>Management</option>
                                <option value="client" {{ $current === 'client' ? 'selected' : '' }}>Client</option>
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
                <div class="rounded-[8px] border border-gray-200 px-3 py-3">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="grant_super_admin" value="1" {{ old('grant_super_admin', $isTargetSuperAdmin) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]">
                        <span class="text-[12px] font-medium text-[#1F2937]">Grant Super Admin</span>
                    </label>
                    <p class="mt-1 text-[11px] text-gray-500">
                        Full access to every company, everything — bypasses per-company roles entirely. Only an Owner
                        can grant this.
                    </p>
                </div>
            @endif
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                Save changes
            </button>
            <a href="{{ route('users.index') }}" class="text-[12px] text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>

    <dialog id="password-modal" class="w-full max-w-sm rounded-[12px] border border-gray-200 p-6 shadow-lg backdrop:bg-black/30">
        <h2 class="text-[14px] font-medium text-[#1F2937]">Change password</h2>

        @if ($errors->has('password'))
            <div class="mt-3 rounded-[8px] bg-red-50 p-3 text-[12px] text-red-700">
                {{ $errors->first('password') }}
            </div>
        @endif

        <form method="POST" action="{{ route('users.password.update', $user) }}" class="mt-4 space-y-4" id="password-form">
            @csrf
            @method('PUT')

            <div>
                <label for="new-password" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">New password</label>
                <input id="new-password" name="password" type="password" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <div>
                <label for="new-password-confirmation" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Confirm new password</label>
                <input id="new-password-confirmation" name="password_confirmation" type="password" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <p class="text-[11px] text-gray-500">At least 8 characters, with upper &amp; lower case, a number, and a symbol.</p>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('password-modal').close()"
                    class="text-[12px] text-gray-600 hover:underline">
                    Cancel
                </button>
                <button type="submit" id="password-submit-btn" disabled
                    class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56] disabled:cursor-not-allowed disabled:opacity-50">
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

        function validateRoles(force) {
            if (! roleSelects.length || ! rolesError) return;
            const hasAccess = Array.from(roleSelects).some(function (select) { return select.value !== 'none'; })
                || (grantSuperAdmin && grantSuperAdmin.checked);
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

        if (grantSuperAdmin) {
            grantSuperAdmin.addEventListener('change', function () { validateRoles(true); });
        }

        document.getElementById('edit-user-form').addEventListener('submit', function (event) {
            validateRoles(true);

            if (rolesError && rolesError.style.display !== 'none') {
                event.preventDefault();
            }
        });
    })();
</script>

@include('users._unsaved-changes-guard', ['formId' => 'edit-user-form'])
@endsection
