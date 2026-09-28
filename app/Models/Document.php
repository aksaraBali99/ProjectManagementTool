<?php

namespace App\Models;

use App\Enums\DocumentAccessLevel;
use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'organization_id', 'uploaded_by', 'name', 'link', 'access_level',
    'storage_key', 'sha256_hash', 'size_bytes', 'mime_type', 'original_filename', 'origin_task_id',
    'folder_id',
])]
class Document extends Model
{
    use BelongsToOrganization;

    /**
     * task #73 phase 1: the ONE place a Document's URL is ever built —
     * every consumer (Documents list, Task's Documents section,
     * file-downloads.show, JSON responses) reads this instead of the raw
     * `link` column, so later storage-access-enforcement work only ever
     * changes this one accessor. Appended to array/JSON output so a
     * fetch()-driven page (e.g. tasks/_documents.blade.php) gets it for
     * free without every call site remembering to ->append('url').
     */
    protected $appends = ['url'];

    protected function casts(): array
    {
        return [
            'access_level' => DocumentAccessLevel::class,
            'size_bytes' => 'integer',
        ];
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => $this->storage_key !== null
            ? Storage::disk(config('filestorage.disk'))->url($this->storage_key)
            : $this->link);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function originTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'origin_task_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DocumentFolder::class, 'folder_id');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_documents');
    }
}
