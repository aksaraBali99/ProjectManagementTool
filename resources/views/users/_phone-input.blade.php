<div>
    <label for="phone" class="block text-[10px] font-semibold uppercase tracking-[0.05em] text-gray-500">Phone <span class="text-red-600">*</span></label>
    <input id="phone" type="tel" value="{{ $phoneValue }}" required
        data-phone-input data-error-target="phone-error" data-hidden-name="phone"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
    <p id="phone-error" class="field-error mt-1 text-[11px] text-red-600" style="display: none;"></p>
    @error('phone')
        <p class="field-error mt-1 text-[11px] text-red-600">{{ $message }}</p>
    @enderror
</div>
