<?php

namespace App\Enums;

use App\Enums\Concerns\HasAdminConfigurableColors;
use App\Enums\Concerns\HasEnumValues;
use App\Models\TaskStatusColor;

enum TaskStatus: string
{
    use HasAdminConfigurableColors, HasEnumValues;

    case Pending = 'pending';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case Completed = 'completed';

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

    private static function colorModel(): string
    {
        return TaskStatusColor::class;
    }

    private static function colorCacheKey(): string
    {
        return 'task_status_colors';
    }

    private static function colorColumn(): string
    {
        return 'status';
    }

    /**
     * Fallback only for the unlikely case a status has no DB row at all —
     * originally the UIX spec's Status Badge Colours table (Pending,
     * Active/in_progress, Need Review/in_review, Completed; its 5th pair
     * "Not Started" has no corresponding case in this enum and was never
     * used here).
     *
     * @return array{background_color: string, text_color: string}
     */
    private function fallbackColors(): array
    {
        return match ($this) {
            self::Pending => ['background_color' => '#F1EFE8', 'text_color' => '#5F5E5A'],
            self::InProgress => ['background_color' => '#E1F5EE', 'text_color' => '#0F6E56'],
            self::InReview => ['background_color' => '#FAEEDA', 'text_color' => '#854F0B'],
            self::Completed => ['background_color' => '#EAF3DE', 'text_color' => '#3B6D11'],
        };
    }
}
