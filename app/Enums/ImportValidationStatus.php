<?php

namespace App\Enums;

enum ImportValidationStatus: string
{
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

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
