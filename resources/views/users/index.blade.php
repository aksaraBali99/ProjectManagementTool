@extends('layouts.authenticated')

@section('title', 'Users — Solava')

@section('content')
<div class="mx-auto max-w-5xl">
    <a href="{{ route('settings.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Settings</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Users</h1>
        <a
            href="{{ route('users.create') }}"
            class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]"
        >
            + Add user
        </a>
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">
            {{ session('status') }}
        </div>
    @endif

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200">
        <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
            <thead class="hidden bg-gray-50 md:table-header-group">
                <tr>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Name</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Username</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Email</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Company roles</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Status</th>
                    <th class="px-3 py-2 text-right text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                @foreach ($users as $user)
                    <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0">
                        <td class="text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">{{ $user->name }}</td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Username</span>
                            {{ $user->username }}
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Email</span>
                            {{ $user->email }}
                        </td>
                        <td class="py-1 md:table-cell md:px-3 md:py-2.5">
                            <span class="mb-1 block text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Company roles</span>
                            @php $globalRoles = $user->roles->whereIn('slug', [\App\Models\Role::SUPER_ADMIN, \App\Models\Role::OWNER]) @endphp
                            @forelse ($globalRoles as $role)
                                <span class="mr-1 inline-block rounded-sm bg-[#E1F5EE] px-2 py-0.5 text-[10px] font-medium text-[#085041]">
                                    {{ $role->name }}
                                </span>
                            @empty
                            @endforelse

                            @forelse ($user->orgMemberships as $membership)
                                <x-badge :background="$membership->organization->badgeBackground()" :text="$membership->organization->badgeText()" class="mr-1">
                                    {{ $membership->organization->name }}: {{ $membership->role->name }}
                                </x-badge>
                            @empty
                                @if ($globalRoles->isEmpty())
                                    <span class="text-[10px] text-gray-400">No company access</span>
                                @endif
                            @endforelse
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Status</span>
                            @if ($user->is_active)
                                <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Active</span>
                            @else
                                <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Inactive</span>
                            @endif
                        </td>
                        <td class="flex items-center justify-end gap-2 py-1 text-[11px] md:table-cell md:px-3 md:py-2.5 md:text-right">
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
