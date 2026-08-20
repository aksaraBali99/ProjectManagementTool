<?php

namespace App\Enums;

enum DocumentAccessLevel: string
{
    case Private = 'private';
    case Internal = 'internal';
    case Public = 'public';

    public function label(): string
    {
        return match ($this) {
            self::Private => 'Private',
            self::Internal => 'Internal',
            self::Public => 'Public',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
