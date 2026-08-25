<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum ImportValidationStatus: string
{
    use HasEnumValues;

    case Valid = 'valid';
    case Warning = 'warning';
    case Error = 'error';

    public function label(): string
    {
        return match ($this) {
            self::Valid => 'Valid',
            self::Warning => 'Warning',
            self::Error => 'Error',
        };
    }
}
