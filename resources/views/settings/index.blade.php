@extends('layouts.authenticated')

@section('title', 'Settings — FounderOS')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-xl font-semibold text-gray-900">Settings</h1>

    <div class="mt-6 space-y-2">
        @can('viewAny', App\Models\User::class)
            <a href="{{ route('users.index') }}"
               class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
                <span>
                    <span class="block text-sm font-medium text-gray-900">Manage users</span>
                    <span class="block text-xs text-gray-500">Add, edit, and deactivate user accounts and company roles</span>
                </span>
                <i class="ti ti-chevron-right text-gray-400"></i>
            </a>
        @endcan
    </div>

    @cannot('viewAny', App\Models\User::class)
        <p class="mt-4 text-sm text-gray-500">No settings are available to you yet.</p>
    @endcannot
</div>
@endsection
