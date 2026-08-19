@extends('layouts.authenticated')

@section('title', 'Projects — FounderOS')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Projects</h1>
        @if ($organization && auth()->user()->can('create', [\App\Models\Project::class, $organization->id]))
            <a href="{{ route('projects.create', $organization) }}"
               class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                + Add project
            </a>
        @endif
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-[8px] bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        {{-- Company tabs, active underline in that company's own accent color --}}
        <div class="mt-4 flex gap-1 border-b border-gray-200">
            @foreach ($organizations as $tab)
                @php $isActiveTab = $organization && $organization->id === $tab->id @endphp
                <a href="{{ route('projects.index', $tab) }}"
                   style="{{ $isActiveTab ? 'border-color: '.$tab->accent_color : '' }}"
                   class="border-b-2 px-4 py-2 text-[12px] {{ $isActiveTab ? 'font-medium text-[#1F2937]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    {{ $tab->name }}
                </a>
            @endforeach
        </div>

        <div class="mt-4 overflow-hidden rounded-[10px] border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Name</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Client</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Tasks</th>
                        <th class="px-3 py-2 text-right text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($projects as $project)
                        <tr>
                            <td class="px-3 py-2.5 text-[12px] font-medium text-[#1F2937]">{{ $project->name }}</td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $project->client_name }}</td>
                            <td class="px-3 py-2.5">
                                @if ($project->status === 'open')
                                    <span class="rounded-[5px] bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">Open</span>
                                @else
                                    <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">Closed</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5">
                                @if ($project->priority === 'high')
                                    <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">High</span>
                                @elseif ($project->priority === 'medium')
                                    <span class="rounded-[5px] bg-[#FDF1D9] px-2 py-0.5 text-[10px] font-medium text-[#8A5A00]">Medium</span>
                                @else
                                    <span class="rounded-[5px] bg-gray-100 px-2 py-0.5 text-[10px] font-medium text-gray-600">Low</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $project->tasks_count }}</td>
                            <td class="px-3 py-2.5 text-right text-[11px]">
                                @can('update', $project)
                                    <a href="{{ route('projects.edit', $project) }}" class="text-[#1D9E75] hover:underline">Edit</a>
                                @endcan
                                @if (! empty(auth()->user()->manageableOrganizationIds()))
                                    <a href="{{ route('projects.template', $project) }}" class="ml-3 text-gray-500 hover:underline">Use as template</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-[12px] text-gray-500">No projects yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
