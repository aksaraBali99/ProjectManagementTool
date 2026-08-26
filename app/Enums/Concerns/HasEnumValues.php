<?php

namespace App\Enums\Concerns;

/**
 * The one-line `values(): array` implementation every backed enum in this
 * app ends up defining identically — pulled out so it's written once.
 */
trait HasEnumValues
{
    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
