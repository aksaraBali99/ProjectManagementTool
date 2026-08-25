<?php

namespace App\Enums;

enum ImportBatchStatus: string
{
    case PendingReview = 'pending_review';
    case Committed = 'committed';
    case PartiallyCommitted = 'partially_committed';
    case Abandoned = 'abandoned';

    public function label(): string
    {
        return match ($this) {
            self::PendingReview => 'Pending Review',
            self::Committed => 'Committed',
            self::PartiallyCommitted => 'Partially Committed',
            self::Abandoned => 'Abandoned',
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
