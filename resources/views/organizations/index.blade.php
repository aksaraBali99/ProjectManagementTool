@extends('layouts.authenticated')

@section('title', 'Companies — Solava')

@section('content')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('settings.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Settings</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Companies</h1>
        <a href="{{ route('organizations.create') }}"
           class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
            + Add company
        </a>
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-[8px] bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    <div class="mt-4 overflow-hidden rounded-[10px] border border-gray-200">
        <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
            <thead class="hidden bg-gray-50 md:table-header-group">
                <tr>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Accent</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Departments</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Projects</th>
                    <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</th>
                    <th class="px-3 py-2 text-right text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                @foreach ($organizations as $organization)
                    <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0">
                        <td class="text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">{{ $organization->name }}</td>
                        <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-400 md:hidden">Accent</span>
                            <span>
                                <span class="inline-block h-3.5 w-3.5 rounded-full align-middle" style="background-color: {{ $organization->accent_color }}"></span>
                                <span class="ml-1 text-[10px] text-gray-500">{{ $organization->accent_color }}</span>
                            </span>
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-400 md:hidden">Departments</span>
                            {{ $organization->departments_count }}
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-400 md:hidden">Projects</span>
                            {{ $organization->projects_count }}
                        </td>
                        <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                            <span class="text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-400 md:hidden">Status</span>
                            @if ($organization->is_active)
                                <span class="rounded-[5px] bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Active</span>
                            @else
                                <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Inactive</span>
                            @endif
                        </td>
                        <td class="flex items-center justify-end gap-2 py-1 text-[11px] md:table-cell md:px-3 md:py-2.5 md:text-right">
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
