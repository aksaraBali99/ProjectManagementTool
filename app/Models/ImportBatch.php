<?php

namespace App\Models;

use App\Enums\ImportBatchStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['uploaded_by', 'file_name', 'stored_path', 'status', 'uuid', 'completed_stages', 'committed_at'])]
class ImportBatch extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ImportBatchStatus::class,
            'completed_stages' => 'array',
            'committed_at' => 'datetime',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function importRows(): HasMany
    {
        return $this->hasMany(ImportRow::class);
    }
}
