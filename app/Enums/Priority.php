<?php

namespace App\Enums;

enum Priority: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function label(): string
    {
        return match ($this) {
            self::High => 'High',
            self::Medium => 'Medium',
            self::Low => 'Low',
        };
    }

    /** Dot / left-border colour, per the UIX spec's Priority Colours table. */
    public function dotColor(): string
    {
        return match ($this) {
            self::High => '#E24B4A',
            self::Medium => '#EF9F27',
            self::Low => '#D4B800',
        };
    }

    public function badgeBackground(): string
    {
        return match ($this) {
            self::High => '#FDEAEA',
            self::Medium => '#FEF5E7',
            self::Low => '#FEFCE6',
        };
    }

    public function badgeText(): string
    {
        return match ($this) {
            self::High => '#A32D2D',
            self::Medium => '#854F0B',
            self::Low => '#706910',
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
