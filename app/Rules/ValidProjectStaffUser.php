<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Re-checks a submitted "Assigned staff" id server-side against the exact
 * same pool the picker itself offers (User::scopeAssignableAsStaffIn()) —
 * org_members for this company (minus Client-role members) UNIONed with
 * global-role users (super_admin/owner) — since the dropdown's option list
 * is trusted client input, not a guarantee.
 */
class ValidProjectStaffUser implements ValidationRule
{
    public function __construct(private readonly int $organizationId) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! User::assignableAsStaffIn($this->organizationId)->whereKey($value)->exists()) {
            $fail('Select a user eligible to be assigned to this company.');
        }
    }
}
