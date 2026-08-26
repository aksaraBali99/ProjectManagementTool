@extends('layouts.authenticated')

@section('title', 'Projects — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Projects</h1>
        @if ($organization && auth()->user()->can('create', [\App\Models\Project::class, $organization->id]))
            <a href="{{ route('projects.create', $organization) }}"
               class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                + Add project
            </a>
        @endif
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-brand-50 px-3 py-2 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="projects.index">
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                <x-table-header>
                    <x-th>Name</x-th>
                    <x-th>Client</x-th>
                    <x-th>Status</x-th>
                    <x-th>Priority</x-th>
                    <x-th>Tasks</x-th>
                    <x-th align="right">Actions</x-th>
                </x-table-header>
                <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                    @forelse ($projects as $project)
                        <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0">
                            <td class="text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">{{ $project->name }}</td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Client</span>
                                {{ $project->primaryClient()->name ?? 'Internal' }}
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Status</span>
                                @if ($project->status === \App\Enums\ProjectStatus::Open)
                                    <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">{{ $project->status->label() }}</span>
                                @else
                                    <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">{{ $project->status->label() }}</span>
                                @endif
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Priority</span>
                                <x-badge :background="$project->priority->badgeBackground()" :text="$project->priority->badgeText()">{{ $project->priority->label() }}</x-badge>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Tasks</span>
                                {{ $project->tasks_count }}
                            </td>
                            <td class="flex items-center justify-end gap-2 py-1 text-[11px] md:table-cell md:px-3 md:py-2.5 md:text-right">
                                @can('update', $project)
                                    <a href="{{ route('projects.edit', $project) }}" class="text-brand-600 hover:underline">Edit</a>
                                @endcan
                                @if (! empty(auth()->user()->manageableOrganizationIds()))
                                    <a href="{{ route('projects.template', $project) }}" class="ml-3 text-gray-500 hover:underline">Use as template</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-empty-table-row colspan="6">No projects yet.</x-empty-table-row>
                    @endforelse
                </tbody>
            </table>
        </div>
        </x-company-tabs>
    @endif
</div>
@endsection
