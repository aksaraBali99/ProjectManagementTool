@extends('layouts.app')

@section('title', 'Users — FounderOS')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">Users</h1>
        <a
            href="{{ route('users.create') }}"
            class="rounded-md bg-[#1D9E75] px-4 py-2 text-sm font-medium text-white hover:bg-[#0F6E56]"
        >
            + Add user
        </a>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-[#E1F5EE] p-3 text-sm text-[#085041]">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Username</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Email</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Company roles</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->username }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            @forelse ($user->orgMemberships as $membership)
                                <span class="mr-1 inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs">
                                    {{ $membership->organization->name }}: {{ ucfirst($membership->role->slug) }}
                                </span>
                            @empty
                                <span class="text-xs text-gray-400">No company access</span>
                            @endforelse
                        </td>
                        <td class="px-4 py-3">
                            @if ($user->is_active)
                                <span class="rounded-full bg-[#EAF3DE] px-2 py-0.5 text-xs text-[#3B6D11]">Active</span>
                            @else
                                <span class="rounded-full bg-[#FCEBEB] px-2 py-0.5 text-xs text-[#A32D2D]">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('users.edit', $user) }}" class="text-[#1D9E75] hover:underline">Edit</a>
                            <form method="POST" action="{{ route('users.toggle-active', $user) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="ml-3 text-gray-500 hover:underline">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
