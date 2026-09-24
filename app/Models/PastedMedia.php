<?php

namespace App\Models;

use App\Enums\FileCategory;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * See the pasted_media migration for what this row is for — a
 * count-and-lock ledger PastedMediaNamer reads/writes atomically, not a
 * user-facing record of its own.
 */
#[Fillable(['organization_id', 'task_id', 'file_type', 'filename'])]
class PastedMedia extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'file_type' => FileCategory::class,
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
