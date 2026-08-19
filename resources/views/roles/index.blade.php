@extends('layouts.authenticated')

@section('title', 'Roles — Solva')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('settings.index') }}" class="text-xs text-gray-500 hover:underline">← Settings</a>

    <h1 class="mt-2 text-xl font-semibold text-gray-900">Roles</h1>
    <p class="mt-1 text-sm text-gray-500">
        System roles power the app's access rules and can't be created or deleted here — only their name and
        description can be edited.
    </p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-[#E1F5EE] p-3 text-sm text-[#085041]">{{ session('status') }}</div>
    @endif

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Slug</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Description</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Users</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($roles as $role)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $role->name }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $role->slug }}
                            @if ($role->is_system)
                                <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500">System</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $role->description }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $role->users_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('roles.edit', $role) }}" class="text-[#1D9E75] hover:underline">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
