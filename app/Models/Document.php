<?php

namespace App\Models;

use App\Enums\DocumentAccessLevel;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['organization_id', 'uploaded_by', 'name', 'link', 'access_level'])]
class Document extends Model
{
    use BelongsToOrganization;

    protected function casts(): array
    {
        return [
            'access_level' => DocumentAccessLevel::class,
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_documents');
    }
}
