@extends('layouts.authenticated')

@section('title', 'Edit user — FounderOS')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('users.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Users</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Edit user</h1>

    @if ($errors->any() && ! $errors->has('password'))
        <div class="mt-4 rounded-[8px] bg-red-50 p-3 text-[12px] text-red-700">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="mt-4 rounded-[8px] bg-[#E1F5EE] p-3 text-[12px] text-[#085041]">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user) }}" class="mt-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="username" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
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
        </div>

        <div>
            <label for="employee_id" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Employee ID</label>
            <input id="employee_id" name="employee_id" type="text" value="{{ old('employee_id', $user->employee_id) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="email" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="phone" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Phone</label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" required
                pattern="^\+?[0-9\s\-\(\)]{7,20}$"
                title="7–15 digits, may include +, spaces, hyphens, and parentheses"
                class="mt-1 block w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

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
                            <select name="roles[{{ $organization->id }}]" class="rounded-[8px] border border-gray-300 px-2 py-1 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                                <option value="none" {{ $current === 'none' ? 'selected' : '' }}>No access</option>
                                <option value="staff" {{ $current === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="management" {{ $current === 'management' ? 'selected' : '' }}>Management</option>
                            </select>
                        </div>
                    @endforeach
                </div>
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
    })();
</script>
@endsection
