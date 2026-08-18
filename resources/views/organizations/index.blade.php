@extends('layouts.authenticated')

@section('title', 'Companies — FounderOS')

@section('content')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('settings.index') }}" class="text-xs text-gray-500 hover:underline">← Settings</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-900">Companies</h1>
        <a href="{{ route('organizations.create') }}"
           class="rounded-md bg-[#1D9E75] px-4 py-2 text-sm font-medium text-white hover:bg-[#0F6E56]">
            + Add company
        </a>
    </div>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-[#E1F5EE] p-3 text-sm text-[#085041]">{{ session('status') }}</div>
    @endif

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Slug</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Accent</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Departments</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Projects</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-500">Status</th>
                    <th class="px-4 py-2 text-right font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($organizations as $organization)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $organization->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $organization->slug }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-block h-4 w-4 rounded-full align-middle" style="background-color: {{ $organization->accent_color }}"></span>
                            <span class="ml-1 text-xs text-gray-500">{{ $organization->accent_color }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $organization->departments_count }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $organization->projects_count }}</td>
                        <td class="px-4 py-3">
                            @if ($organization->is_active)
                                <span class="rounded-full bg-[#EAF3DE] px-2 py-0.5 text-xs text-[#3B6D11]">Active</span>
                            @else
                                <span class="rounded-full bg-[#FCEBEB] px-2 py-0.5 text-xs text-[#A32D2D]">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('organizations.edit', $organization) }}" class="text-[#1D9E75] hover:underline">Edit</a>
                            <form method="POST" action="{{ route('organizations.toggle-active', $organization) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="ml-3 text-gray-500 hover:underline">
                                    {{ $organization->is_active ? 'Deactivate' : 'Activate' }}
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
