@extends('layouts.authenticated')

@section('title', 'Departments — Solava')

@section('content')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('settings.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Settings</a>

    <div class="mt-2 flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Departments</h1>
        <a href="{{ route('departments.create') }}"
           class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
            + Add department
        </a>
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">No active companies yet.</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="departments.index">
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                <thead class="hidden bg-gray-50 md:table-header-group">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Name</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Color</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Tasks</th>
                        <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Status</th>
                        <th class="px-3 py-2 text-right text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                    @forelse ($departments as $department)
                        <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0">
                            <td class="flex items-center gap-2 md:table-cell md:px-3 md:py-2.5">
                                <span class="inline-block h-3.5 w-3.5 shrink-0 rounded-full align-middle md:hidden" style="background-color: {{ $department->color }}"></span>
                                <span class="text-[12px] font-medium text-[#1F2937]">{{ $department->name }}</span>
                            </td>
                            <td class="hidden md:table-cell md:px-3 md:py-2.5">
                                <span class="inline-block h-3.5 w-3.5 rounded-full align-middle" style="background-color: {{ $department->color }}"></span>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Tasks</span>
                                {{ $department->tasks_count }}
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Status</span>
                                @if ($department->is_active)
                                    <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Active</span>
                                @else
                                    <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Inactive</span>
                                @endif
                            </td>
                            <td class="flex items-center justify-end gap-2 py-1 text-[11px] md:table-cell md:px-3 md:py-2.5 md:text-right">
                                <a href="{{ route('departments.edit', $department) }}" class="text-[#1D9E75] hover:underline">Edit</a>
                                <form method="POST" action="{{ route('departments.toggle-active', $department) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="ml-3 text-gray-500 hover:underline">
                                        {{ $department->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="block md:table-row">
                            <td colspan="5" class="block px-3 py-4 text-center text-[12px] text-gray-500 md:table-cell">No departments yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </x-company-tabs>
    @endif
</div>
@endsection
