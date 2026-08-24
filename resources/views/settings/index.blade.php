@extends('layouts.authenticated')

@section('title', 'Settings — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-xl font-semibold text-gray-900">Settings</h1>

    @php
        $hasAnyCard = auth()->user()->can('viewAny', App\Models\User::class)
            || auth()->user()->can('viewAny', App\Models\Organization::class)
            || auth()->user()->can('viewAny', App\Models\Department::class)
            || auth()->user()->can('viewAny', App\Models\Role::class)
            || auth()->user()->can('viewAny', App\Models\AuditLog::class)
            || auth()->user()->can('analytics.view')
            || auth()->user()->can('task-colors.view');
    @endphp

    <div class="mt-6 space-y-2">
        {{-- Always visible — everyone can configure their own notification
             preferences, no permission required for that part of the page. --}}
        <a href="{{ route('notification-settings.index') }}"
           class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
            <span>
                <span class="block text-sm font-medium text-gray-900">Notification settings</span>
                <span class="block text-xs text-gray-500">Choose which events notify you, and how</span>
            </span>
            <i class="ti ti-chevron-right text-gray-400"></i>
        </a>

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

        @can('viewAny', App\Models\Organization::class)
            <a href="{{ route('organizations.index') }}"
               class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
                <span>
                    <span class="block text-sm font-medium text-gray-900">Manage companies</span>
                    <span class="block text-xs text-gray-500">Add, edit, and activate/deactivate companies</span>
                </span>
                <i class="ti ti-chevron-right text-gray-400"></i>
            </a>
        @endcan

        @can('viewAny', App\Models\Department::class)
            <a href="{{ route('departments.index') }}"
               class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
                <span>
                    <span class="block text-sm font-medium text-gray-900">Manage departments</span>
                    <span class="block text-xs text-gray-500">Add, edit, and activate/deactivate task-type departments per company</span>
                </span>
                <i class="ti ti-chevron-right text-gray-400"></i>
            </a>
        @endcan

        @can('viewAny', App\Models\Role::class)
            <a href="{{ route('roles.index') }}"
               class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
                <span>
                    <span class="block text-sm font-medium text-gray-900">Manage roles</span>
                    <span class="block text-xs text-gray-500">Edit role names and descriptions</span>
                </span>
                <i class="ti ti-chevron-right text-gray-400"></i>
            </a>
        @endcan

        @can('viewAny', App\Models\AuditLog::class)
            <a href="{{ route('audit-trail.index') }}"
               class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
                <span>
                    <span class="block text-sm font-medium text-gray-900">Audit trail</span>
                    <span class="block text-xs text-gray-500">Read-only history of task, subtask, and comment changes</span>
                </span>
                <i class="ti ti-chevron-right text-gray-400"></i>
            </a>
        @endcan

        @can('analytics.view')
            <a href="{{ route('analytics.index') }}"
               class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
                <span>
                    <span class="block text-sm font-medium text-gray-900">Analytics</span>
                    <span class="block text-xs text-gray-500">Cross-company task completion, status, priority, and staff workload</span>
                </span>
                <i class="ti ti-chevron-right text-gray-400"></i>
            </a>
        @endcan

        @can('task-colors.view')
            <a href="{{ route('task-colors.edit') }}"
               class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-3 hover:bg-gray-50">
                <span>
                    <span class="block text-sm font-medium text-gray-900">Status & Priority Colors</span>
                    <span class="block text-xs text-gray-500">Customize the colors used for task statuses and priorities app-wide</span>
                </span>
                <i class="ti ti-chevron-right text-gray-400"></i>
            </a>
        @endcan
    </div>

    @unless ($hasAnyCard)
        <p class="mt-4 text-sm text-gray-500">No settings are available to you yet.</p>
    @endunless
</div>
@endsection
