@extends('layouts.authenticated')

@section('title', 'Import Complete — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('import.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Bulk Import</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Import #{{ $summary->batch->id }} committed</h1>
    <p class="mt-1 text-[11px] text-gray-500">{{ $summary->batch->file_name }}</p>

    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200">
        <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Sheet</th>
                    <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Result</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($summary->countsBySheet as $sheetName => $counts)
                    <tr>
                        <td class="px-3 py-2.5 text-[12px] font-medium text-[#1F2937]">{{ $sheetName }}</td>
                        <td class="px-3 py-2.5 text-[11px] text-gray-600">
                            {{ collect($counts)->filter()->map(fn ($count, $kind) => "{$count} {$kind}")->implode(', ') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-3 py-6 text-center text-[12px] text-gray-500">No rows were committed.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (! empty($summary->temporaryPasswords))
        <div class="mt-4 rounded-lg border border-[#FCA5A5] bg-[#FEF2F2] p-3">
            <p class="text-[11px] font-medium text-[#B91C1C]">
                Copy these temporary passwords now — they will not be shown again.
            </p>
            <table class="mt-2 w-full">
                <tbody>
                    @foreach ($summary->temporaryPasswords as $username => $password)
                        <tr>
                            <td class="py-0.5 text-[11px] text-[#1F2937]">{{ $username }}</td>
                            <td class="py-0.5 font-mono text-[11px] text-[#1F2937]">{{ $password }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
