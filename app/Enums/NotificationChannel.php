<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum NotificationChannel: string
{
    use HasEnumValues;

    case InApp = 'in_app';
    case Email = 'email';

    public function label(): string
    {
        return match ($this) {
            self::InApp => 'In-app',
            self::Email => 'Email',
        };
    }
}
