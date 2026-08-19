<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\ValidationResult;

/**
 * Validates a phone number using Google's libphonenumber data (the same
 * dataset intl-tel-input's client-side check uses), so the two stay
 * consistent with each other. Expects E.164 (e.g. "+6281234567890") — what
 * the picker's hidden input submits — but falls back to Indonesia as the
 * default region for a bare national-format number without a leading "+".
 */
class ValidPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            $fail('Please enter valid phone number');

            return;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            $number = $util->parse($value, 'ID');
        } catch (NumberParseException) {
            $fail('Please enter valid phone number');

            return;
        }

        $message = match ($util->isPossibleNumberWithReason($number)) {
            ValidationResult::TOO_SHORT => 'Phone number is too short.',
            ValidationResult::TOO_LONG => 'Phone number is too long.',
            ValidationResult::INVALID_COUNTRY_CODE => 'Please enter a valid country code.',
            default => $util->isValidNumber($number) ? null : 'Please enter valid phone number',
        };

        if ($message !== null) {
            $fail($message);
        }
    }
}
