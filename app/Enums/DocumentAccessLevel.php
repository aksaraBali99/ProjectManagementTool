<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum DocumentAccessLevel: string
{
    use HasEnumValues;

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
}
