@extends('layouts.authenticated')

@section('title', 'Status & Priority Colors — Solava')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-[14px] font-medium text-[#1F2937]">Status & Priority Colors</h1>
    <p class="mt-1 text-[11px] text-gray-500">
        These colors are used everywhere a task's status or priority is shown — Kanban, Dashboard, task lists, and Analytics charts.
    </p>

    @if (session('status'))
        <div class="mt-4 rounded-md bg-[#E1F5EE] p-3 text-[12px] text-[#085041]">{{ session('status') }}</div>
    @endif

    <div class="mt-6">
        <h2 class="text-[11px] font-semibold uppercase tracking-[0.05em] text-gray-500">Status Colors</h2>

        <form method="POST" action="{{ route('task-colors.update-status') }}" class="mt-2">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                @foreach ($statusColors as $color)
                    @php
                        $bg = old('colors.'.$color['value'].'.background_color', $color['background_color']);
                        $text = old('colors.'.$color['value'].'.text_color', $color['text_color']);
                    @endphp
                    <div class="task-color-row flex flex-wrap items-center gap-3 rounded-md border border-gray-200 px-3 py-3" data-value="{{ $color['value'] }}">
                        <span class="w-24 shrink-0 text-[12px] font-medium text-[#1F2937]">{{ $color['label'] }}</span>

                        <label class="flex items-center gap-1.5 text-[10px] text-gray-500">
                            Background
                            <input type="color" class="task-color-bg h-7 w-10 cursor-pointer rounded border border-gray-300 p-0"
                                name="colors[{{ $color['value'] }}][background_color]" value="{{ $bg }}">
                        </label>

                        <label class="flex items-center gap-1.5 text-[10px] text-gray-500">
                            Text
                            <input type="color" class="task-color-text h-7 w-10 cursor-pointer rounded border border-gray-300 p-0"
                                name="colors[{{ $color['value'] }}][text_color]" value="{{ $text }}">
                        </label>

                        <x-badge class="task-color-preview" :background="$bg" :text="$text">{{ $color['label'] }}</x-badge>

                        <span class="task-color-contrast-warning text-[10px] text-amber-600" style="display: none;"></span>
                    </div>
                @endforeach
            </div>

            @error('colors')
                <p class="mt-2 text-[11px] text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-4">
                <button type="submit" class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                    Save status colors
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8">
        <h2 class="text-[11px] font-semibold uppercase tracking-[0.05em] text-gray-500">Priority Colors</h2>

        <form method="POST" action="{{ route('task-colors.update-priority') }}" class="mt-2">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                @foreach ($priorityColors as $color)
                    @php
                        $bg = old('priority_colors.'.$color['value'].'.background_color', $color['background_color']);
                        $text = old('priority_colors.'.$color['value'].'.text_color', $color['text_color']);
                    @endphp
                    <div class="task-color-row flex flex-wrap items-center gap-3 rounded-md border border-gray-200 px-3 py-3" data-value="{{ $color['value'] }}">
                        <span class="w-24 shrink-0 text-[12px] font-medium text-[#1F2937]">{{ $color['label'] }}</span>

                        <label class="flex items-center gap-1.5 text-[10px] text-gray-500">
                            Background
                            <input type="color" class="task-color-bg h-7 w-10 cursor-pointer rounded border border-gray-300 p-0"
                                name="priority_colors[{{ $color['value'] }}][background_color]" value="{{ $bg }}">
                        </label>

                        <label class="flex items-center gap-1.5 text-[10px] text-gray-500">
                            Text
                            <input type="color" class="task-color-text h-7 w-10 cursor-pointer rounded border border-gray-300 p-0"
                                name="priority_colors[{{ $color['value'] }}][text_color]" value="{{ $text }}">
                        </label>

                        <x-badge class="task-color-preview" :background="$bg" :text="$text">{{ $color['label'] }}</x-badge>

                        <span class="task-color-contrast-warning text-[10px] text-amber-600" style="display: none;"></span>
                    </div>
                @endforeach
            </div>

            @error('priority_colors')
                <p class="mt-2 text-[11px] text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-4">
                <button type="submit" class="rounded-md bg-[#1D9E75] px-4 py-2 text-[12px] font-medium text-white hover:bg-[#0F6E56]">
                    Save priority colors
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Shared by every .task-color-row on this page — both the Status and
     Priority sections above reuse the exact same row markup/classes —
     live preview swatch + a non-blocking WCAG AA contrast warning (ratio
     below 4.5:1, the threshold for normal-size text), computed client-side so
     nothing round-trips to the server before saving. --}}
<script>
    (function () {
        function hexToRgb(hex) {
            const clean = hex.replace('#', '');
            return {
                r: parseInt(clean.substring(0, 2), 16),
                g: parseInt(clean.substring(2, 4), 16),
                b: parseInt(clean.substring(4, 6), 16),
            };
        }

        function relativeLuminance(rgb) {
            const channels = [rgb.r, rgb.g, rgb.b].map(function (channel) {
                const value = channel / 255;
                return value <= 0.03928 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4);
            });

            return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
        }

        function contrastRatio(hexA, hexB) {
            const luminanceA = relativeLuminance(hexToRgb(hexA));
            const luminanceB = relativeLuminance(hexToRgb(hexB));
            const lighter = Math.max(luminanceA, luminanceB);
            const darker = Math.min(luminanceA, luminanceB);

            return (lighter + 0.05) / (darker + 0.05);
        }

        document.querySelectorAll('.task-color-row').forEach(function (row) {
            const bgInput = row.querySelector('.task-color-bg');
            const textInput = row.querySelector('.task-color-text');
            const preview = row.querySelector('.task-color-preview');
            const warning = row.querySelector('.task-color-contrast-warning');

            function render() {
                preview.style.backgroundColor = bgInput.value;
                preview.style.color = textInput.value;

                const ratio = contrastRatio(bgInput.value, textInput.value);
                if (ratio < 4.5) {
                    warning.textContent = 'Low contrast (' + ratio.toFixed(1) + ':1) — WCAG AA recommends at least 4.5:1.';
                    warning.style.display = '';
                } else {
                    warning.style.display = 'none';
                }
            }

            bgInput.addEventListener('input', render);
            textInput.addEventListener('input', render);
            render();
        });
    })();
</script>
@endsection
