@once
    <script>
        (function () {
            function errorElementFor(input) {
                let el = input.parentElement.querySelector(':scope > .field-error');
                if (! el) {
                    el = document.createElement('p');
                    el.className = 'field-error mt-1 text-[11px] text-red-600';
                    input.insertAdjacentElement('afterend', el);
                }
                return el;
            }

            function showError(input, message) {
                errorElementFor(input).textContent = message;
                input.classList.add('border-red-400');
            }

            function clearError(input) {
                const el = input.parentElement.querySelector(':scope > .field-error');
                if (el) el.remove();
                input.classList.remove('border-red-400');
            }

            function messageFor(input) {
                if (input.validity.valueMissing) {
                    return input.validationMessage;
                }
                if (input.type === 'email' && input.validity.typeMismatch) {
                    return 'Please enter valid email address';
                }
                if (input.type === 'tel' && input.validity.patternMismatch) {
                    return 'Please enter valid phone number';
                }
                return input.validationMessage;
            }

            function validate(input) {
                if (input.checkValidity()) {
                    clearError(input);
                } else {
                    showError(input, messageFor(input));
                }
            }

            function isValidatable(el) {
                return el.matches('input[required], input[type="email"], select[required]');
            }

            document.addEventListener('blur', function (event) {
                if (isValidatable(event.target)) {
                    validate(event.target);
                }
            }, true);

            document.addEventListener('input', function (event) {
                if (isValidatable(event.target) && event.target.parentElement.querySelector(':scope > .field-error')) {
                    validate(event.target);
                }
            });

            document.addEventListener('change', function (event) {
                if (isValidatable(event.target) && event.target.tagName === 'SELECT') {
                    validate(event.target);
                }
            });

            document.querySelectorAll('form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    form.querySelectorAll('input[required], input[type="email"], select[required]').forEach(validate);

                    if (! form.checkValidity()) {
                        event.preventDefault();
                        const firstInvalid = form.querySelector(':invalid');
                        if (firstInvalid) firstInvalid.focus();
                    }
                });
            });
        })();
    </script>
@endonce
