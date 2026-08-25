<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum ProjectStatus: string
{
    use HasEnumValues;

    case Open = 'open';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Closed => 'Closed',
        };
    }
}
