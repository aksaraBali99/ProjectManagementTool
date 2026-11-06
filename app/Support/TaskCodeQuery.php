<?php

namespace App\Support;

/**
 * task #118 phase 4: reads a search box entry as a task code, so the Tasks
 * page can find BGS-0012 however it's typed.
 *
 * Accepted (case-insensitive, surrounding spaces trimmed, leading zeros
 * ignored): "BGS-0012", "BGS-12", "BGS12", "BGS 12" (a company code and a
 * number), and "0012", "12", "#12" (a number alone, meaning "in this
 * company"). Anything else, including a number of 0, isn't a code.
 */
final class TaskCodeQuery
{
    /** Up to 9 digits, so the number always fits an int. */
    private const PATTERN = '/^(?:(?<code>[A-Za-z]{3})[-\s]?(?<coded>\d{1,9})|#?(?<number>\d{1,9}))$/';

    /**
     * @return array{code: string|null, number: int}|null
     */
    public static function parse(string $query): ?array
    {
        if (! preg_match(self::PATTERN, trim($query), $match)) {
            return null;
        }

        $code = ($match['code'] ?? '') === '' ? null : strtoupper($match['code']);
        // A group from the branch that didn't match is '' or missing.
        $number = (int) ($code === null ? ($match['number'] ?? '') : ($match['coded'] ?? ''));

        return $number > 0 ? ['code' => $code, 'number' => $number] : null;
    }
}
