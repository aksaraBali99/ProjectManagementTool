<?php

namespace App\Enums;

use App\Models\TaskStatusColor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case Completed = 'completed';

    private const COLOR_CACHE_KEY = 'task_status_colors';

    /** Display text per the UIX spec's Status Badge Colours table. */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'Active',
            self::InReview => 'Need Review',
            self::Completed => 'Completed',
        };
    }

    /**
     * Admin-configurable via the Status & Priority Colors settings page
     * (task_status_colors table, cached forever and invalidated on save —
     * see forgetColorCache()). The match below is now only a fallback for
     * the unlikely case a status has no row at all, not the live source of
     * truth — originally the UIX spec's Status Badge Colours table
     * (Pending, Active/in_progress, Need Review/in_review, Completed; its
     * 5th pair "Not Started" has no corresponding case in this enum and was
     * never used here).
     */
    public function badgeBackground(): string
    {
        return self::colorRow($this)?->background_color ?? match ($this) {
            self::Pending => '#F1EFE8',
            self::InProgress => '#E1F5EE',
            self::InReview => '#FAEEDA',
            self::Completed => '#EAF3DE',
        };
    }

    public function badgeText(): string
    {
        return self::colorRow($this)?->text_color ?? match ($this) {
            self::Pending => '#5F5E5A',
            self::InProgress => '#0F6E56',
            self::InReview => '#854F0B',
            self::Completed => '#3B6D11',
        };
    }

    private static function colorRow(self $status): ?TaskStatusColor
    {
        return self::allColors()->get($status->value);
    }

    /**
     * @return Collection<string, TaskStatusColor>
     */
    private static function allColors(): Collection
    {
        return Cache::rememberForever(self::COLOR_CACHE_KEY, fn () => TaskStatusColor::all()->keyBy('status'));
    }

    /**
     * Called from the Status & Priority Colors settings page after saving
     * — badge colors are cached forever (not TTL-based), so nothing else
     * ever clears this; every screen that renders a status badge would
     * otherwise keep serving stale colors until the cache store itself
     * expires or is flushed.
     */
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
