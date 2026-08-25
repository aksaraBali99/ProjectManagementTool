<?php

namespace App\Models;

use App\Enums\ImportRowAction;
use App\Enums\ImportValidationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['import_batch_id', 'sheet_name', 'row_number', 'raw_data', 'resolved_action', 'validation_status', 'validation_message', 'entity_id'])]
class ImportRow extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
            'resolved_action' => ImportRowAction::class,
            'validation_status' => ImportValidationStatus::class,
        ];
    }

    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class);
    }
}
