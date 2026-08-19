@extends('layouts.authenticated')

@section('title', 'Users — Solava')

@section('content')
<div class="mx-auto max-w-5xl">
    <a href="{{ route('settings.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Settings</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Users</h1>
        <a
            href="{{ route('users.create') }}"
            class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]"
        >
            + Add user
        </a>
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-[8px] bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-4 overflow-hidden rounded-[10px] border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Username</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Email</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Company roles</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</th>
                    <th class="px-3 py-2 text-right text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-3 py-2.5 text-[12px] font-medium text-[#1F2937]">{{ $user->name }}</td>
                        <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $user->username }}</td>
                        <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $user->email }}</td>
                        <td class="px-3 py-2.5">
                            @php $globalRoles = $user->roles->whereIn('slug', [\App\Models\Role::SUPER_ADMIN, \App\Models\Role::OWNER]) @endphp
                            @forelse ($globalRoles as $role)
                                <span class="mr-1 inline-block rounded-[5px] bg-[#E1F5EE] px-2 py-0.5 text-[10px] font-medium text-[#085041]">
                                    {{ $role->name }}
                                </span>
                            @empty
                            @endforelse

                            @forelse ($user->orgMemberships as $membership)
                                <span class="mr-1 inline-block rounded-[5px] bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-700">
                                    {{ $membership->organization->name }}: {{ ucfirst($membership->role->slug) }}
                                </span>
                            @empty
                                @if ($globalRoles->isEmpty())
                                    <span class="text-[10px] text-gray-400">No company access</span>
                                @endif
                            @endforelse
                        </td>
                        <td class="px-3 py-2.5">
                            @if ($user->is_active)
                                <span class="rounded-[5px] bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Active</span>
                            @else
                                <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Inactive</span>
                            @endif
                        </td>
                        <td class="px-3 py-2.5 text-right text-[11px]">
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
