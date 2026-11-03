<?php

namespace App\Support;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * task #118: a company's 3-letter code (BGS, SLV, ...), the prefix of its
 * task codes (BGS-0012). Exactly three uppercase letters, unique across
 * companies.
 *
 * suggest() is the default rule for a company that has no code yet: the
 * first three letters of its name, or the next free combination if those
 * are taken. Used by the task #118 migration outside production (where
 * the fixed name map doesn't cover dev/test companies) and by
 * Organization::creating() until the company page asks for the code
 * itself. It is only ever a suggestion — production's codes come from
 * the migration's fixed map.
 */
final class CompanyCode
{
    public static function isValid(mixed $code): bool
    {
        return is_string($code) && preg_match('/^[A-Z]{3}$/', $code) === 1;
    }

    /**
     * The first free code for $name, trying in order:
     *  1. the name's first three letters (Bali Hire -> BAL);
     *  2. its first two letters plus each later letter of the name
     *     (DiagOrg: DIA, DIG, DIO, DIR);
     *  3. its first two letters plus A..Z, then its first letter plus
     *     AA..ZZ, then AAA..ZZZ.
     * Letters only (accents folded to ASCII); a name with fewer than
     * three letters is padded with X.
     *
     * @param  iterable<string|null>  $taken  codes already in use
     */
    public static function suggest(string $name, iterable $taken): string
    {
        $used = [];
        foreach ($taken as $code) {
            if (is_string($code)) {
                $used[strtoupper($code)] = true;
            }
        }

        foreach (self::candidates($name) as $candidate) {
            if (! isset($used[$candidate])) {
                return $candidate;
            }
        }

        throw new RuntimeException('Every 3-letter company code is taken.');
    }

    /** @return iterable<string> */
    private static function candidates(string $name): iterable
    {
        $letters = str_pad(preg_replace('/[^A-Z]/', '', strtoupper(Str::ascii($name))), 3, 'X');
        $alphabet = range('A', 'Z');

        yield substr($letters, 0, 3);

        for ($i = 3; $i < strlen($letters); $i++) {
            yield $letters[0].$letters[1].$letters[$i];
        }

        foreach ($alphabet as $third) {
            yield $letters[0].$letters[1].$third;
        }

        foreach ($alphabet as $second) {
            foreach ($alphabet as $third) {
                yield $letters[0].$second.$third;
            }
        }

        foreach ($alphabet as $first) {
            foreach ($alphabet as $second) {
                foreach ($alphabet as $third) {
                    yield $first.$second.$third;
                }
            }
        }
    }
}
