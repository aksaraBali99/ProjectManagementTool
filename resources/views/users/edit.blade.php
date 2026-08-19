@extends('layouts.authenticated')

@section('title', 'Edit user — FounderOS')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('users.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Users</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Edit user</h1>

    @if (session('status'))
        <div class="mt-4 rounded-[8px] bg-[#E1F5EE] p-3 text-[12px] text-[#085041]">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user) }}" class="mt-6 space-y-4" novalidate>
        @csrf
        @method('PUT')

        <div>
            <label for="username" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Username</label>
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
            <label for="name" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('name')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="employee_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Employee ID</label>
            <input id="employee_id" name="employee_id" type="text" value="{{ old('employee_id', $user->employee_id) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            @error('employee_id')
                <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        @include('users._contact-rows', [
            'fieldName' => 'emails',
            'heading' => 'Email addresses',
            'helpText' => 'At least one email is required. Add more with whatever label makes sense.',
            'inputType' => 'email',
            'valuePlaceholder' => 'name@example.com',
            'defaultLabel' => 'Email',
            'otherLabel' => 'email',
            'presets' => ['Work email', 'Personal email'],
            'rows' => collect(old('emails', $user->emails->map(fn ($email) => ['label' => $email->label, 'value' => $email->email])->all())),
        ])

        @include('users._contact-rows', [
            'fieldName' => 'phones',
            'heading' => 'Phone numbers',
            'helpText' => 'At least one phone number is required. Add more with whatever label makes sense.',
            'inputType' => 'tel',
            'valuePlaceholder' => 'e.g. +62 812 3456 7890',
            'defaultLabel' => 'Phone number',
            'otherLabel' => 'phone number',
            'presets' => ['Mobile phone number', 'Office phone number', 'Home phone number'],
            'rows' => collect(old('phones', $user->phones->map(fn ($phone) => ['label' => $phone->label, 'value' => $phone->phone])->all())),
        ])

        @if ($globalRoles->isNotEmpty())
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
                <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Company roles</span>
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
                            </select>
                        </div>
                    @endforeach
                </div>
                <p id="roles-error" class="field-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
                @error('roles')
                    <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
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

        <form method="POST" action="{{ route('users.password.update', $user) }}" class="mt-4 space-y-4" id="password-form" novalidate>
            @csrf
            @method('PUT')

            <div>
                <label for="new-password" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">New password</label>
                <input id="new-password" name="password" type="password" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <p id="new-password-hint" class="mt-1 text-[11px] text-gray-500">At least 8 characters, with upper &amp; lower case, a number, and a symbol.</p>
                @error('password')
                    <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="new-password-confirmation" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Confirm new password</label>
                <input id="new-password-confirmation" name="password_confirmation" type="password" required
                    class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                <p id="password-match-error" class="field-error mt-1 text-[11px] text-red-600" style="display: none;">Passwords do not match.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('password-modal').close()"
                    class="text-[12px] text-gray-600 hover:underline">
                    Cancel
                </button>
                <button type="submit" id="password-submit-btn"
                    class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
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
        const passwordHint = document.getElementById('new-password-hint');
        const matchError = document.getElementById('password-match-error');
        const modal = document.getElementById('password-modal');

        function isStrong(value) {
            return value.length >= 8
                && /[a-z]/.test(value)
                && /[A-Z]/.test(value)
                && /[0-9]/.test(value)
                && /[^A-Za-z0-9]/.test(value);
        }

        function validatePassword(showIfInvalid) {
            const strong = isStrong(password.value);
            passwordHint.classList.toggle('text-red-600', ! strong && showIfInvalid);
            passwordHint.classList.toggle('text-gray-500', strong || ! showIfInvalid);
        }

        function validateMatch(showIfInvalid) {
            const matches = confirmation.value.length > 0 && password.value === confirmation.value;
            matchError.style.display = (! matches && showIfInvalid) ? '' : 'none';
        }

        password.addEventListener('blur', function () { validatePassword(true); });
        password.addEventListener('input', function () {
            validatePassword(passwordHint.classList.contains('text-red-600'));
            if (confirmation.value) validateMatch(matchError.style.display !== 'none');
        });
        confirmation.addEventListener('blur', function () { validateMatch(true); });
        confirmation.addEventListener('input', function () {
            validateMatch(matchError.style.display !== 'none');
        });

        document.getElementById('password-form').addEventListener('submit', function (event) {
            validatePassword(true);
            validateMatch(true);

            if (! isStrong(password.value) || password.value !== confirmation.value) {
                event.preventDefault();
            }
        });

        const roleSelects = document.querySelectorAll('.role-select');
        const rolesError = document.getElementById('roles-error');

        function validateRoles(force) {
            if (! roleSelects.length || ! rolesError) return;
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

        @if ($errors->has('password'))
            modal.showModal();
        @endif
    })();
</script>
@endsection
