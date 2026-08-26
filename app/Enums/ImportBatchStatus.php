<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumValues;

enum ImportBatchStatus: string
{
    use HasEnumValues;

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
}
