<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^\+?[0-9\s\-\(\)]+$/', $value)) {
            $fail('Please enter valid phone number');

            return;
        }

        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) < 7 || strlen($digits) > 15) {
            $fail('Please enter valid phone number');
        }
    }
}
