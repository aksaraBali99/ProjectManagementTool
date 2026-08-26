@extends('layouts.authenticated')

@section('title', 'Documents — Solava')

@section('content')
<div>
    <div class="flex items-center justify-between">
        <h1 class="text-[14px] font-medium text-[#1F2937]">Documents</h1>
        @if ($organization && $canManage)
            <a href="{{ route('documents.create', $organization) }}"
               class="rounded-md bg-brand-600 px-4 py-2 text-[12px] font-medium text-white hover:bg-brand-700">
                + Add new document
            </a>
        @endif
    </div>

    @if (session('status'))
        <div class="mt-3 rounded-md bg-brand-50 px-3 py-2 text-[12px] text-brand-800">{{ session('status') }}</div>
    @endif

    @if ($organizations->isEmpty())
        <p class="mt-6 text-[12px] text-gray-500">You don't have access to any companies yet.</p>
    @else
        <x-company-tabs :organizations="$organizations" :active="$organization" route="documents.index">
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                <x-table-header>
                    <x-th>Name</x-th>
                    <x-th>Access level</x-th>
                    <x-th>Uploaded by</x-th>
                </x-table-header>
                <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                    @forelse ($documents as $document)
                        <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0">
                            <td class="text-[12px] font-medium text-[#1F2937] md:table-cell md:px-3 md:py-2.5">
                                <a href="{{ $document->link }}" target="_blank" rel="noopener noreferrer" class="hover:underline">{{ $document->name }}</a>
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Access level</span>
                                @if ($document->access_level === \App\Enums\DocumentAccessLevel::Private)
                                    <span class="rounded-sm bg-[#FCEBEB] px-2 py-0.5 text-[10px] font-medium text-[#A32D2D]">{{ $document->access_level->label() }}</span>
                                @elseif ($document->access_level === \App\Enums\DocumentAccessLevel::Internal)
                                    <span class="rounded-sm bg-[#FDF1D9] px-2 py-0.5 text-[10px] font-medium text-[#8A5A00]">{{ $document->access_level->label() }}</span>
                                @else
                                    <span class="rounded-sm bg-[#EAF3DE] px-2 py-0.5 text-[10px] font-medium text-[#3B6D11]">{{ $document->access_level->label() }}</span>
                                @endif
                            </td>
                            <td class="flex items-center justify-between gap-2 py-1 text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">
                                <span class="text-[10px] font-medium uppercase tracking-[0.06em] text-gray-400 md:hidden">Uploaded by</span>
                                <span class="inline-flex items-center gap-1.5">
                                    <x-avatar :user="$document->uploader" size="16px" />
                                    {{ $document->uploader->name }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <x-empty-table-row colspan="3" py="6">No documents yet.</x-empty-table-row>
                    @endforelse
                </tbody>
            </table>
        </div>
        </x-company-tabs>
    @endif
</div>
@endsection
