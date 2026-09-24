<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * A cached fetch result for one URL, global across the whole app — see
 * the migration's own docblock for why this isn't tenant-scoped and isn't
 * consulted at render time. LinkPreviewService is the only thing that
 * reads or writes this table.
 */
#[Fillable(['url_hash', 'url', 'title', 'image_url', 'domain', 'fetched_at'])]
class LinkPreview extends Model
{
    protected function casts(): array
    {
        return [
            'fetched_at' => 'datetime',
        ];
    }
}
