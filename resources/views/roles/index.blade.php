@extends('layouts.authenticated')

@section('title', 'Roles — Solava')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('settings.index') }}" class="text-xs text-gray-500 hover:underline">← Settings</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">Roles</h1>
        <a href="{{ route('roles.permissions.edit') }}" class="rounded-md bg-[#1D9E75] px-4 py-2 text-sm font-medium text-white hover:bg-[#0F6E56]">
            Manage permissions
        </a>
    </div>
    <p class="mt-1 text-sm text-gray-500">
        System roles power the app's access rules and can't be created or deleted here — only their name and
        description can be edited. What each role can actually do is configured under
        <a href="{{ route('roles.permissions.edit') }}" class="text-[#1D9E75] hover:underline">Manage permissions</a>.
    </p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-[#E1F5EE] p-3 text-sm text-[#085041]">{{ session('status') }}</div>
    @endif

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
        <table class="w-full text-sm md:min-w-full md:divide-y md:divide-gray-200">
            <thead class="hidden bg-gray-50 md:table-header-group">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Description</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Users</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                @foreach ($roles as $role)
                    <tr class="block px-4 py-3 md:table-row md:px-0 md:py-0">
                        <td class="text-gray-900 md:table-cell md:px-4 md:py-3">
                            {{ $role->name }}
                            @if ($role->is_system)
                                <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">System</span>
                            @endif
                        </td>
                        <td class="py-1 text-gray-600 md:table-cell md:px-4 md:py-3">
                            <span class="mb-0.5 block text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Description</span>
                            {{ $role->description }}
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-gray-600 md:table-cell md:px-4 md:py-3">
                            <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Users</span>
                            {{ $role->users_count }}
                        </td>
                        <td class="py-1 text-right md:table-cell md:px-4 md:py-3">
                            <a href="{{ route('roles.edit', $role) }}" class="text-[#1D9E75] hover:underline">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
