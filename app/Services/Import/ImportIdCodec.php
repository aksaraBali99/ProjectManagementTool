<?php

namespace App\Services\Import;

/**
 * Encodes/decodes the "ID (do not edit)" column on Companies/Departments/
 * Projects — a human-readable prefix + zero-padded primary key (e.g.
 * "CO-0001"), not a separate mapping table. Encoding a real primary key
 * directly makes decoding on upload a trivial regex + intval, and the
 * template builder (encode) and upload parser (decode) sharing this one
 * class means the two directions can never drift apart.
 */
class ImportIdCodec
{
    public const COMPANY_PREFIX = 'CO';

    public const DEPARTMENT_PREFIX = 'DP';

    public const PROJECT_PREFIX = 'PRJ';

    public static function encode(string $prefix, int $id): string
    {
        return sprintf('%s-%04d', $prefix, $id);
    }

    public static function decode(string $prefix, ?string $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (! preg_match('/^'.preg_quote($prefix, '/').'-(\d+)$/', $value, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }
}
