@extends('layouts.authenticated')

@section('title', 'Import Review — Solava')

@php
    $actionColors = [
        'insert' => ['bg' => '#E1F5EE', 'text' => '#0F6E56'],
        'update' => ['bg' => '#EEEDFE', 'text' => '#4338CA'],
        'no_change' => ['bg' => '#F1EFE8', 'text' => '#5F5E5A'],
        'sync' => ['bg' => '#FAEEDA', 'text' => '#854F0B'],
        'blocked' => ['bg' => '#FDEAEA', 'text' => '#A32D2D'],
    ];
    $statusColors = [
        'valid' => ['bg' => '#E1F5EE', 'text' => '#0F6E56'],
        'warning' => ['bg' => '#FEF5E7', 'text' => '#854F0B'],
        'error' => ['bg' => '#FDEAEA', 'text' => '#A32D2D'],
    ];
@endphp

@section('content')
<div>
    <a href="{{ route('import.index') }}" class="text-[10px] uppercase tracking-[0.05em] text-gray-500 hover:underline">← Bulk Import</a>

    <h1 class="mt-2 text-[14px] font-medium text-[#1F2937]">Review Import #{{ $batch->id }}</h1>
    <p class="mt-1 text-[11px] text-gray-500">{{ $batch->file_name }} — nothing is written to Solava until you commit below.</p>

    <div class="mt-4 flex flex-wrap items-center gap-3 rounded-lg border border-gray-200 bg-white p-3">
        <x-badge :background="$errorCount > 0 ? $statusColors['error']['bg'] : $statusColors['valid']['bg']" :text="$errorCount > 0 ? $statusColors['error']['text'] : $statusColors['valid']['text']">
            {{ $errorCount }} {{ Str::plural('error', $errorCount) }}
        </x-badge>
        <x-badge :background="$warningCount > 0 ? $statusColors['warning']['bg'] : $statusColors['valid']['bg']" :text="$warningCount > 0 ? $statusColors['warning']['text'] : $statusColors['valid']['text']">
            {{ $warningCount }} {{ Str::plural('warning', $warningCount) }}
        </x-badge>

        @if ($warningCount > 0)
            <label class="flex items-center gap-1.5 text-[11px] text-gray-600">
                <input type="checkbox" id="acknowledge-warnings" class="rounded border-gray-300 text-[#1D9E75] focus:ring-[#1D9E75]">
                I've reviewed the warnings above and want to proceed anyway
            </label>
        @endif

        <form method="POST" action="{{ route('import.commit', $batch) }}" class="ml-auto">
            @csrf
            <input type="hidden" name="acknowledge_warnings" id="acknowledge-warnings-field" value="0">
            <button type="submit" id="commit-button" disabled
                class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56] disabled:cursor-not-allowed disabled:opacity-40">
                Commit Import
            </button>
        </form>
    </div>

    @foreach ($rowsBySheet as $sheetName => $rows)
        @if ($rows->isNotEmpty())
            <div class="mt-4 overflow-hidden rounded-lg border border-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-3 py-2">
                    <h2 class="text-[11px] font-semibold uppercase tracking-[0.05em] text-gray-600">{{ $sheetName }} ({{ $rows->count() }})</h2>
                </div>
                <table class="w-full md:min-w-full md:divide-y md:divide-gray-200">
                    <thead class="hidden bg-white md:table-header-group">
                        <tr>
                            <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Row</th>
                            <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Action</th>
                            <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Status</th>
                            <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Data</th>
                            <th class="px-3 py-2 text-left text-[10px] font-medium uppercase tracking-[0.06em] text-gray-500">Message</th>
                        </tr>
                    </thead>
                    <tbody class="block divide-y divide-gray-100 bg-white md:table-row-group">
                        @foreach ($rows as $row)
                            @php
                                $action = $row->resolved_action->value;
                                $status = $row->validation_status->value;
                            @endphp
                            <tr class="block px-3 py-2.5 md:table-row md:px-0 md:py-0">
                                <td class="whitespace-nowrap text-[11px] text-gray-500 md:table-cell md:px-3 md:py-2.5">{{ $row->row_number }}</td>
                                <td class="py-1 md:table-cell md:px-3 md:py-2.5">
                                    <x-badge :background="$actionColors[$action]['bg']" :text="$actionColors[$action]['text']">{{ $row->resolved_action->label() }}</x-badge>
                                </td>
                                <td class="py-1 md:table-cell md:px-3 md:py-2.5">
                                    <x-badge :background="$statusColors[$status]['bg']" :text="$statusColors[$status]['text']">{{ $row->validation_status->label() }}</x-badge>
                                </td>
                                <td class="py-1 text-[11px] text-gray-600 md:table-cell md:px-3 md:py-2.5">
                                    {{ collect($row->raw_data)->filter()->map(fn ($v, $k) => "{$k}: {$v}")->implode(' · ') }}
                                </td>
                                <td class="py-1 text-[11px] text-gray-600 md:table-cell md:px-3 md:py-2.5">
                                    {{ $row->validation_message ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    <script>
        (function () {
            var commitButton = document.getElementById('commit-button');
            var acknowledgeCheckbox = document.getElementById('acknowledge-warnings');
            var acknowledgeField = document.getElementById('acknowledge-warnings-field');
            var errorCount = {{ $errorCount }};
            var warningCount = {{ $warningCount }};

            function refresh() {
                var acknowledged = acknowledgeCheckbox && acknowledgeCheckbox.checked;
                var warningsClear = warningCount === 0 || acknowledged;
                commitButton.disabled = errorCount > 0 || ! warningsClear;
                acknowledgeField.value = acknowledged ? '1' : '0';
            }

            if (acknowledgeCheckbox) {
                acknowledgeCheckbox.addEventListener('change', refresh);
            }

            refresh();
        })();
    </script>
</div>
@endsection
