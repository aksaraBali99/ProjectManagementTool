<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;

/**
 * SOL-0022: the one place the start/due date rules compare dates, so the
 * task form, the subtask endpoints, Add Task and Bulk Import can't drift
 * apart. Works on 'Y-m-d' strings, which sort correctly as strings: no
 * time of day, no time zones.
 */
class DateRange
{
    public const DUE_BEFORE_START = "The due date can't be earlier than the start date.";

    /**
     * A date as 'Y-m-d', or null when empty. Callers pass values that
     * already passed the 'date' rule (or a date cast), so parsing can't
     * fail here.
     */
    public static function normalize(mixed $value): ?string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return Carbon::parse((string) $value)->toDateString();
    }

    /** True only when both dates are set and the due date is earlier. Same day is fine. */
    public static function dueBeforeStart(?string $start, ?string $due): bool
    {
        return $start !== null && $start !== '' && $due !== null && $due !== ''
            && $due < $start;
    }

    /**
     * The latest of the given dates, ignoring empty ones; null when none.
     *
     * @param  iterable<?string>  $dates
     */
    public static function latest(iterable $dates): ?string
    {
        $latest = null;
        foreach ($dates as $date) {
            if ($date !== null && $date !== '' && ($latest === null || $date > $latest)) {
                $latest = $date;
            }
        }

        return $latest;
    }

    /** A 'Y-m-d' date as it reads in messages: "10 Dec 2026". */
    public static function format(string $date): string
    {
        return Carbon::parse($date)->format('j M Y');
    }
}
