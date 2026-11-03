{{--
    task #118 phase 2: the company's task code field (BVA, as in
    BVA-0001), on Add company and on Edit company while the code is still
    unlocked. The suggestion comes from organizations.code-suggestion
    (CompanyCode::suggest()); the rule is never copied into JS.

    Expects:
      $value        the field's value
      $except       null on Add; the company's id on Edit, so its own
                    code doesn't count as taken
      $suggestOnBlur  true on Add: leaving Name fills the code while the
                    field is empty or still holds the last suggestion
      $suggestButton  true on Edit: a "Suggest from name" button
--}}
<div>
    <label for="code" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Task code</label>
    <div class="mt-1 flex items-center gap-2">
        <input id="code" name="code" type="text" value="{{ $value }}" required maxlength="3" autocomplete="off" spellcheck="false"
            data-suggest-url="{{ route('organizations.code-suggestion') }}"
            @if ($except) data-except="{{ $except }}" @endif
            @if ($suggestOnBlur) data-suggest-on-blur @endif
            class="block w-24 rounded-md border border-gray-300 px-3 py-2 font-mono text-[12px] uppercase tracking-[0.1em] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
        @if ($suggestButton)
            <button type="button" id="code-suggest" class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-[11px] font-medium text-gray-700 hover:bg-gray-50">
                Suggest from name
            </button>
        @endif
    </div>
    <p class="mt-1 text-[10px] text-gray-500">3 letters, shown before every task number, e.g. BVA-0001. It can't be changed once the company has tasks.</p>
    <p id="code-preview" class="mt-1 text-[11px] text-gray-600"></p>
    @error('code')
        <p class="mt-1 text-[10px] text-[#A32D2D]">{{ $message }}</p>
    @enderror
</div>

<script>
    (function () {
        const field = document.getElementById('code');
        const name = document.getElementById('name');
        const preview = document.getElementById('code-preview');
        const button = document.getElementById('code-suggest');
        // The last code this form filled in: while the field still holds
        // it, the user hasn't typed their own, so a new name may replace it.
        let lastSuggestion = '';

        function renderPreview() {
            const code = field.value;
            preview.textContent = /^[A-Z]{3}$/.test(code)
                ? 'Tasks will be numbered ' + code + '-0001, ' + code + '-0002, ...'
                : 'Enter 3 letters to see how tasks will be numbered.';
        }

        field.addEventListener('input', function () {
            const caret = field.selectionStart;
            field.value = field.value.toUpperCase();
            field.setSelectionRange(caret, caret);
            renderPreview();
        });

        // fromBlur: only fill a field the user hasn't typed their own code
        // into, checked again when the answer arrives (they may have typed
        // one while it was on its way). The button always fills.
        function canFill(fromBlur) {
            return ! fromBlur || field.value === '' || field.value === lastSuggestion;
        }

        function suggest(fromBlur) {
            const url = new URL(field.dataset.suggestUrl, window.location.origin);
            url.searchParams.set('name', name.value);
            if (field.dataset.except) url.searchParams.set('except', field.dataset.except);

            fetch(url.toString(), { credentials: 'same-origin', headers: { Accept: 'application/json' } })
                .then(function (response) { return response.ok ? response.json() : null; })
                .then(function (data) {
                    if (! data || ! data.code || ! canFill(fromBlur)) return;
                    field.value = data.code;
                    lastSuggestion = data.code;
                    renderPreview();
                })
                // No suggestion (offline, session ended): the field stays as
                // it is and can be typed in by hand.
                .catch(function () {});
        }

        if ('suggestOnBlur' in field.dataset) {
            name.addEventListener('blur', function () {
                if (name.value.trim() === '') return;
                if (canFill(true)) suggest(true);
            });
        }
        if (button) button.addEventListener('click', function () { suggest(false); });

        renderPreview();
    })();
</script>
