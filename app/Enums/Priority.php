<?php

namespace App\Enums;

use App\Enums\Concerns\HasAdminConfigurableColors;
use App\Enums\Concerns\HasEnumValues;
use App\Models\TaskPriorityColor;

enum Priority: string
{
    use HasAdminConfigurableColors, HasEnumValues;

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

    private static function colorModel(): string
    {
        return TaskPriorityColor::class;
    }

    private static function colorCacheKey(): string
    {
        return 'task_priority_colors';
    }

    private static function colorColumn(): string
    {
        return 'priority';
    }

    /**
     * @return array{background_color: string, text_color: string}
     */
    private function fallbackColors(): array
    {
        return match ($this) {
            self::High => ['background_color' => '#FDEAEA', 'text_color' => '#A32D2D'],
            self::Medium => ['background_color' => '#FEF5E7', 'text_color' => '#854F0B'],
            self::Low => ['background_color' => '#FEFCE6', 'text_color' => '#706910'],
        };
    }
}
