<?php

namespace App\Enums;

use App\Models\TaskPriorityColor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

enum Priority: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    private const COLOR_CACHE_KEY = 'task_priority_colors';

    public function label(): string
    {
        return match ($this) {
            self::High => 'High',
            self::Medium => 'Medium',
            self::Low => 'Low',
        };
    }

    /**
     * Admin-configurable via the Status & Priority Colors settings page
     * (task_priority_colors table, cached forever and invalidated on save
     * — see forgetColorCache()). The match below is now only a fallback
     * for the unlikely case a priority has no row at all.
     */
    public function badgeBackground(): string
    {
        return self::colorRow($this)?->background_color ?? match ($this) {
            self::High => '#FDEAEA',
            self::Medium => '#FEF5E7',
            self::Low => '#FEFCE6',
        };
    }

    public function badgeText(): string
    {
        return self::colorRow($this)?->text_color ?? match ($this) {
            self::High => '#A32D2D',
            self::Medium => '#854F0B',
            self::Low => '#706910',
        };
    }

    private static function colorRow(self $priority): ?TaskPriorityColor
    {
        return self::allColors()->get($priority->value);
    }

    /**
     * @return Collection<string, TaskPriorityColor>
     */
    private static function allColors(): Collection
    {
        return Cache::rememberForever(self::COLOR_CACHE_KEY, fn () => TaskPriorityColor::all()->keyBy('priority'));
    }

    /** Called from the Status & Priority Colors settings page after saving. */
    public static function forgetColorCache(): void
    {
        Cache::forget(self::COLOR_CACHE_KEY);
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
