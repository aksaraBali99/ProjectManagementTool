<?php

namespace App\Services\Import;

use Carbon\Carbon;

/**
 * Label-to-value and date-parsing logic shared between validation-time
 * diffing (ImportValidator, deciding Update vs No Change) and commit-time
 * writes (ImportCommitService) — both need the exact same resolution so a
 * row the review grid calls "No Change" never turns out different once
 * actually committed.
 */
class ImportFieldResolver
{
    /**
     * @param  array<int, \BackedEnum>  $cases  each case must expose a label() method
     */
    public static function resolveEnumValue(array $cases, string $label): string
    {
        foreach ($cases as $case) {
            if (strtolower($case->label()) === strtolower(trim($label))) {
                return $case->value;
            }
        }

        return $cases[0]->value;
    }

    public static function parseDate(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
}
