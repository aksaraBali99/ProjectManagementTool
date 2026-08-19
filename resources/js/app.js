import intlTelInput from 'intl-tel-input/intlTelInputWithUtils';
import 'intl-tel-input/dist/css/intlTelInput.css';

function initPhoneInputs() {
    document.querySelectorAll('[data-phone-input]').forEach(function (input) {
        if (input.dataset.itiInitialized) return;
        input.dataset.itiInitialized = 'true';

        const iti = intlTelInput(input, {
            initialCountry: 'id',
            separateDialCode: true,
            strictMode: true,
            countrySearch: true,
            // The visible input intentionally has no `name` — only the hidden
            // input the library creates (E.164-formatted) gets submitted.
            hiddenInputs: function () {
                return { phone: input.dataset.hiddenName };
            },
        });

        const errorEl = document.getElementById(input.dataset.errorTarget);

        function showError(message) {
            if (! errorEl) return;
            errorEl.textContent = message;
            errorEl.style.display = '';
            input.closest('.iti').classList.add('iti--invalid');
        }

        function clearError() {
            if (! errorEl) return;
            errorEl.style.display = 'none';
            input.closest('.iti').classList.remove('iti--invalid');
        }

        function messageForValidationError(code) {
            switch (code) {
                case intlTelInput.VALIDATION_ERROR.INVALID_COUNTRY_CODE:
                    return 'Please enter a valid country code.';
                case intlTelInput.VALIDATION_ERROR.TOO_SHORT:
                    return 'Phone number is too short.';
                case intlTelInput.VALIDATION_ERROR.TOO_LONG:
                    return 'Phone number is too long.';
                default:
                    return 'Please enter valid phone number';
            }
        }

        function validate(force) {
            if (input.value.trim() === '') {
                if (force) showError('Please fill out this field.');
                else clearError();
                return;
            }

            if (iti.isValidNumberPrecise()) {
                clearError();
            } else if (force) {
                showError(messageForValidationError(iti.getValidationError()));
            }
        }

        input.addEventListener('blur', function () { validate(true); });
        input.addEventListener('input', function () {
            validate(!! (errorEl && errorEl.style.display !== 'none'));
        });
        input.addEventListener('countrychange', function () {
            validate(!! (errorEl && errorEl.style.display !== 'none'));
        });

        const form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function (event) {
                validate(true);
                if (errorEl && errorEl.style.display !== 'none') {
                    event.preventDefault();
                }
            });
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPhoneInputs);
} else {
    initPhoneInputs();
}
