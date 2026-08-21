@extends('layouts.authenticated')

@section('title', 'Documents — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Documents</h1>
        @if ($organization && $canManage)
            <a href="{{ route('documents.create', $organization) }}"
               class="rounded-[8px] bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                + Add new document
            </a>
        @endif
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-[8px] bg-[#E1F5EE] px-3 py-2 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        <div class="mt-4 flex gap-1 border-b border-gray-200">
            @foreach ($organizations as $tab)
                @php $isActiveTab = $organization && $organization->id === $tab->id @endphp
                <a href="{{ route('documents.index', $tab) }}"
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
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Access level</th>
                        <th class="px-3 py-2 text-left text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Uploaded by</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($documents as $document)
                        <tr>
                            <td class="px-3 py-2.5 text-[12px] font-medium text-[#1F2937]">
                                <a href="{{ $document->link }}" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ $document->name }}</a>
                            </td>
                            <td class="px-3 py-2.5">
                                @if ($document->access_level === \App\Enums\DocumentAccessLevel::Private)
                                    <span class="rounded-[5px] bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">{{ $document->access_level->label() }}</span>
                                @elseif ($document->access_level === \App\Enums\DocumentAccessLevel::Internal)
                                    <span class="rounded-[5px] bg-[#FDF1D9] px-2 py-0.5 text-[10px] font-medium text-[#8A5A00]">{{ $document->access_level->label() }}</span>
                                @else
                                    <span class="rounded-[5px] bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">{{ $document->access_level->label() }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-[11px] text-gray-500">{{ $document->uploader->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-6 text-center text-[12px] text-gray-500">No documents yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
