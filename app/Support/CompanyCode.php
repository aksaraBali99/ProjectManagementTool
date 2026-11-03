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
     *  1. task #118 phase 2: a code built from the name's words —
     *     - 3 or more words: the first three words' initials
     *       (Bali Virtual Academy -> BVA, Bali Goods Sourcing -> BGS);
     *     - 2 words: the first word's first two letters and the second
     *       word's first letter (English Hub -> ENH, Bali Hire -> BAH);
     *     - 1 word: its first letter and the next two consonants
     *       (Solava -> SLV), topped up from its remaining letters when
     *       it has fewer;
     *     padded from the name's other letters, then X, if short. This
     *     reproduces every production code in the task #118 migration;
     *  2. the name's first three letters;
     *  3. its first two letters plus each later letter of the name;
     *  4. its first two letters plus A..Z, then its first letter plus
     *     AA..ZZ, then AAA..ZZZ.
     * Letters only (accents folded to ASCII). A name with no letters at
     * all gets XXX, then the next free code.
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
        $ascii = strtoupper(Str::ascii($name));
        $words = array_values(array_filter(preg_split('/[^A-Z]+/', $ascii)));
        $letters = str_pad(implode('', $words), 3, 'X');
        $alphabet = range('A', 'Z');

        yield self::fromWords($words);

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

    /**
     * The first choice, from the name's words (see suggest()).
     *
     * @param  list<string>  $words  uppercase, letters only
     */
    private static function fromWords(array $words): string
    {
        $all = implode('', $words);

        $code = match (true) {
            count($words) >= 3 => $words[0][0].$words[1][0].$words[2][0],
            count($words) === 2 => substr($words[0], 0, 2).$words[1][0],
            count($words) === 1 => $words[0][0].substr(preg_replace('/[AEIOU]/', '', substr($words[0], 1)), 0, 2),
            default => '',
        };

        // Short (a one-letter first word, or a one-word name with fewer
        // than two consonants): top up from the name's letters not used
        // yet, in order, then X.
        if (strlen($code) < 3) {
            $unused = $all;
            foreach (str_split($code) as $letter) {
                $position = strpos($unused, $letter);
                if ($position !== false) {
                    $unused = substr_replace($unused, '', $position, 1);
                }
            }
            $code = substr($code.$unused, 0, 3);
        }

        return str_pad($code, 3, 'X');
    }
}
