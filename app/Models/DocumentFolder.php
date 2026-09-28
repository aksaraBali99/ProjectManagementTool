<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * task #73 phase 2: a folder on the Documents page. Folders have no
 * visibility of their own — every viewer holding view_documents in the
 * company sees every folder in it; only what's INSIDE a folder is
 * filtered per viewer, by the normal Document access rules. parent_id
 * never changes after creation (moving a folder is out of scope this
 * phase, see DocumentFolderPolicy).
 */
#[Fillable(['organization_id', 'parent_id', 'name', 'created_by'])]
class DocumentFolder extends Model
{
    use BelongsToOrganization;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DocumentFolder::class, 'parent_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'folder_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
