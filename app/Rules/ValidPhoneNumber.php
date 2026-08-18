<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^\+?[0-9\s\-\(\)]+$/', $value)) {
            $fail('The :attribute may only contain digits, spaces, hyphens, parentheses, and an optional leading +.');

            return;
        }

        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) < 7 || strlen($digits) > 15) {
            $fail('The :attribute must contain between 7 and 15 digits.');
        }
    }
}
