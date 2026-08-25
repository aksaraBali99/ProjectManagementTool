{{-- Password field with a show/hide toggle button. Usage:
     @include('users._password-input', ['id' => 'password', 'name' => 'password', 'autocomplete' => 'new-password']) --}}
<div class="mt-1 flex items-center gap-2">
    <input id="{{ $id }}" name="{{ $name }}" type="password" required
        @isset($autocomplete) autocomplete="{{ $autocomplete }}" @endisset
        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-[12px] focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
    <button type="button" class="password-toggle-btn flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-gray-300 text-gray-500 hover:bg-gray-50" tabindex="-1" aria-label="Show password">
        <i class="ti ti-eye text-[14px]"></i>
    </button>
</div>

@once
    <script>
        (function () {
            document.addEventListener('click', function (event) {
                const btn = event.target.closest('.password-toggle-btn');
                if (! btn) return;

                const input = btn.previousElementSibling;
                if (! input || input.tagName !== 'INPUT') return;

                const nowShowing = input.type === 'password';
                input.type = nowShowing ? 'text' : 'password';

                const icon = btn.querySelector('i');
                icon.className = nowShowing ? 'ti ti-eye-off text-[14px]' : 'ti ti-eye text-[14px]';
                btn.setAttribute('aria-label', nowShowing ? 'Hide password' : 'Show password');
            });
        })();
    </script>
@endonce
