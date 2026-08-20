<?php

namespace App\Rules;

use App\Models\OrgMember;
use App\Models\Role;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * The Project "Client" picker only offers users who currently hold the
 * Client role in some company — this re-checks that server-side, since the
 * dropdown's option list is trusted client input, not a guarantee.
 */
class ValidClientUser implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isClient = OrgMember::where('user_id', $value)
            ->whereHas('role', fn ($query) => $query->where('slug', Role::CLIENT))
            ->exists();

        if (! $isClient) {
            $fail('Select a valid client, or leave blank for Internal.');
        }
    }
}
