<?php

namespace App\Enums;

enum TaskStatus: string
{
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

    /**
     * Per the UIX spec's Status Badge Colours table: Pending, Active
     * (in_progress), Need Review (in_review), Completed. The spec's 5th
     * colour pair ("Not Started", #FCEBEB/#A32D2D) has no corresponding
     * value in this 4-case enum and is deliberately unused here.
     */
    public function badgeBackground(): string
    {
        return match ($this) {
            self::Pending => '#F1EFE8',
            self::InProgress => '#E1F5EE',
            self::InReview => '#FAEEDA',
            self::Completed => '#EAF3DE',
        };
    }

    public function badgeText(): string
    {
        return match ($this) {
            self::Pending => '#5F5E5A',
            self::InProgress => '#0F6E56',
            self::InReview => '#854F0B',
            self::Completed => '#3B6D11',
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
