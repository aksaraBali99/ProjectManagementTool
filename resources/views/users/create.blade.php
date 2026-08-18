@extends('layouts.app')

@section('title', 'Add user — FounderOS')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8">
    <h1 class="text-xl font-semibold text-gray-900">Add user</h1>

    @if ($errors->any())
        <div class="mt-4 rounded-md bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.store') }}" class="mt-6 space-y-4" id="create-user-form">
        @csrf

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input id="username" name="username" type="text" value="{{ old('username') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input id="password" name="password" type="password" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
            <p class="mt-1 text-xs text-gray-500">At least 8 characters, with upper &amp; lower case, a number, and a symbol.</p>
        </div>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
            <input id="employee_id" name="employee_id" type="text" value="{{ old('employee_id') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
            <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
        </div>

        <div>
            <span class="block text-sm font-medium text-gray-700">Company roles</span>
            <p class="mt-1 text-xs text-gray-500">Assign this user a role in at least one company.</p>
            <div class="mt-2 space-y-2">
                @foreach ($organizations as $organization)
                    <div class="flex items-center justify-between rounded-md border border-gray-200 px-3 py-2">
                        <span class="text-sm text-gray-900">{{ $organization->name }}</span>
                        <select name="roles[{{ $organization->id }}]" class="rounded-md border border-gray-300 px-2 py-1 text-sm">
                            <option value="none" {{ old('roles.'.$organization->id, 'none') === 'none' ? 'selected' : '' }}>No access</option>
                            <option value="staff" {{ old('roles.'.$organization->id) === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="management" {{ old('roles.'.$organization->id) === 'management' ? 'selected' : '' }}>Management</option>
                        </select>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" id="submit-btn"
                class="rounded-md bg-[#1D9E75] px-4 py-2 text-sm font-medium text-white hover:bg-[#0F6E56] disabled:cursor-not-allowed disabled:opacity-50">
                Create user
            </button>
            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
        </div>
    </form>
</div>

<script>
    (function () {
        const password = document.getElementById('password');
        const submitBtn = document.getElementById('submit-btn');

        function isStrong(value) {
            return value.length >= 8
                && /[a-z]/.test(value)
                && /[A-Z]/.test(value)
                && /[0-9]/.test(value)
                && /[^A-Za-z0-9]/.test(value);
        }

        function validate() {
            submitBtn.disabled = !isStrong(password.value);
        }

        password.addEventListener('input', validate);
        validate();
    })();
</script>
@endsection
