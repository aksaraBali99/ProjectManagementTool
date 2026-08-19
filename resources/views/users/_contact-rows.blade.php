@php
    $presets = $presets ?? [];
    $rows = $rows->isEmpty() ? [['label' => '', 'value' => '']] : $rows;
@endphp
<div>
    <span class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">{{ $heading }}</span>
    <p class="mt-1 text-[11px] text-gray-500">{{ $helpText }}</p>

    <div id="{{ $fieldName }}-rows" class="mt-2 space-y-2">
        @foreach ($rows as $index => $row)
            <div class="contact-row flex items-start gap-2">
                <select class="preset-select w-40 shrink-0 rounded-[8px] border border-gray-300 px-2 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    <option value="">Choose a label…</option>
                    @foreach ($presets as $preset)
                        <option value="{{ $preset }}">{{ $preset }}</option>
                    @endforeach
                    <option value="__other__" class="italic">Other {{ $otherLabel }}…</option>
                </select>

                <input type="text" name="{{ $fieldName }}[{{ $index }}][label]" value="{{ $row['label'] ?? '' }}"
                    placeholder="{{ $defaultLabel }}"
                    class="label-input w-40 shrink-0 rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] placeholder:italic placeholder:text-gray-400 focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">

                <div class="flex-1">
                    <input type="{{ $inputType }}" name="{{ $fieldName }}[{{ $index }}][value]" value="{{ $row['value'] ?? '' }}" required
                        placeholder="{{ $valuePlaceholder }}"
                        class="value-input w-full rounded-[8px] border border-gray-300 px-3 py-2 text-[12px] focus:border-[#1D9E75] focus:outline-none focus:ring-1 focus:ring-[#1D9E75]">
                    @error($fieldName.'.'.$index.'.value')
                        <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="button" class="remove-row shrink-0 pt-2 text-[11px] text-gray-500 hover:underline">Remove</button>
            </div>
        @endforeach
    </div>

    @error($fieldName)
        <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
    @enderror

    <button type="button" class="add-row mt-2 text-[11px] font-medium text-[#1D9E75] hover:underline" data-target="{{ $fieldName }}-rows">
        + Add {{ $otherLabel }}
    </button>
</div>

@once
    <script>
        (function () {
            function nextIndex(container) {
                let max = -1;
                container.querySelectorAll('.value-input').forEach(function (input) {
                    const match = input.name.match(/\[(\d+)\]/);
                    if (match) {
                        max = Math.max(max, parseInt(match[1], 10));
                    }
                });
                return max + 1;
            }

            function reindexRow(row, index) {
                row.querySelectorAll('[name]').forEach(function (el) {
                    el.name = el.name.replace(/\[\d+\]/, '[' + index + ']');
                });
            }

            function updateRemoveVisibility(container) {
                const rows = container.querySelectorAll('.contact-row');
                rows.forEach(function (row) {
                    row.querySelector('.remove-row').style.visibility = rows.length > 1 ? 'visible' : 'hidden';
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[id$="-rows"]').forEach(updateRemoveVisibility);
            });

            document.addEventListener('click', function (event) {
                const addBtn = event.target.closest('.add-row');
                if (addBtn) {
                    const container = document.getElementById(addBtn.dataset.target);
                    const row = container.querySelector('.contact-row').cloneNode(true);

                    row.querySelectorAll('input').forEach(function (input) { input.value = ''; });
                    row.querySelectorAll('select').forEach(function (select) { select.value = ''; });
                    row.querySelectorAll('.field-error').forEach(function (el) { el.remove(); });
                    row.querySelectorAll('.border-red-400').forEach(function (el) { el.classList.remove('border-red-400'); });

                    reindexRow(row, nextIndex(container));
                    container.appendChild(row);
                    updateRemoveVisibility(container);
                    return;
                }

                const removeBtn = event.target.closest('.remove-row');
                if (removeBtn) {
                    const container = removeBtn.closest('[id$="-rows"]');
                    if (container.querySelectorAll('.contact-row').length <= 1) return;

                    removeBtn.closest('.contact-row').remove();
                    updateRemoveVisibility(container);
                }
            });

            document.addEventListener('change', function (event) {
                if (! event.target.classList.contains('preset-select')) return;

                const row = event.target.closest('.contact-row');
                const labelInput = row.querySelector('.label-input');

                if (event.target.value === '__other__') {
                    labelInput.value = '';
                    labelInput.focus();
                } else {
                    labelInput.value = event.target.value;
                }
            });
        })();
    </script>
@endonce
