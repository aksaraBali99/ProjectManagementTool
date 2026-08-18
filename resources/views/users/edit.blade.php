@extends('layouts.app')

@section('title', 'Edit user — FounderOS')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8">
    <h1 class="text-xl font-semibold text-gray-900">Edit user</h1>

    @if ($errors->any() && ! $errors->has('password'))
        <div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="mt-4 rounded-md bg-[#E1F5EE] p-3 text-sm text-[#085041]">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user) }}" class="mt-6 space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1 flex items-center gap-3">
                <input type="password" value="********" disabled
                    class="block w-full rounded-md border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-400">
                <button type="button" onclick="document.getElementById('password-modal').showModal()"
                    class="whitespace-nowrap rounded-md border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Change Password
                </button>
            </div>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
            <input id="employee_id" name="employee_id" type="text" value="{{ old('employee_id', $user->employee_id) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-700">Company roles</span>
            <p class="mt-1 text-xs text-gray-500">Assign this user a role in at least one company.</p>
            <div class="mt-2 space-y-2">
                @foreach ($organizations as $organization)
                    @php $current = old('roles.'.$organization->id, $currentRoles[$organization->id] ?? 'none') @endphp
                    <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                        <span class="text-sm text-gray-900">{{ $organization->name }}</span>
                        <select name="roles[{{ $organization->id }}]" class="rounded-md border border-gray-300 px-2 py-1 text-sm">
                            <option value="none" {{ $current === 'none' ? 'selected' : '' }}>No access</option>
                            <option value="staff" {{ $current === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="management" {{ $current === 'management' ? 'selected' : '' }}>Management</option>
                        </select>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                class="rounded-md bg-[#1D9E75] px-4 py-2 text-sm font-medium text-white hover:bg-[#0F6E56]">
                Save changes
            </button>
            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>

    <dialog id="password-modal" class="w-full max-w-sm rounded-lg border border-gray-200 p-6 shadow-lg backdrop:bg-black/30">
        <h2 class="text-lg font-semibold text-gray-900">Change password</h2>

        @if ($errors->has('password'))
            <div class="mt-3 rounded-md bg-red-50 p-3 text-sm text-red-700">
                {{ $errors->first('password') }}
            </div>
        @endif

        <form method="POST" action="{{ route('users.password.update', $user) }}" class="mt-4 space-y-4" id="password-form">
            @csrf
            @method('PUT')

            <div>
                <label for="new-password" class="block text-sm font-medium text-gray-700">New password</label>
                <input id="new-password" name="password" type="password" required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <div>
                <label for="new-password-confirmation" class="block text-sm font-medium text-gray-700">Confirm new password</label>
                <input id="new-password-confirmation" name="password_confirmation" type="password" required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            </div>

            <p class="text-xs text-gray-500">At least 8 characters, with upper &amp; lower case, a number, and a symbol.</p>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('password-modal').close()"
                    class="text-sm text-gray-600 hover:underline">
                    Cancel
                </button>
                <button type="submit" id="password-submit-btn" disabled
                    class="rounded-md bg-[#1D9E75] px-4 py-2 text-sm font-medium text-white hover:bg-[#0F6E56] disabled:cursor-not-allowed disabled:opacity-50">
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
