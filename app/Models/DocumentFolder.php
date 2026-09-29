<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * task #73 phase 4: tasks this folder is linked to as a whole (via
     * task_folder_links) — distinct from Document::tasks() (a single
     * file's own direct links). Used for the "linked to N tasks" indicator
     * on the Documents page and the folder-delete block.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_folder_links', 'folder_id', 'task_id')
            ->withPivot('linked_by')
            ->withTimestamps();
    }

    /**
     * task #73 phase 4: the folder picker's own query — folders in
     * $task's company, excluding ones already linked to it. No access-
     * level/visibility filtering at all (unlike DocumentPolicy::
     * attachableInCompany(), its file-picker equivalent): folders have no
     * visibility of their own (see this class's own docblock above), so
     * "browsable" here means only "same company, not already linked" —
     * TaskPolicy::attachDocuments() (checked by the caller before this
     * query ever runs) is what keeps a Client from reaching this at all.
     *
     * @return Builder<DocumentFolder>
     */
    public function scopeAttachableTo(Builder $query, Task $task): Builder
    {
        return $query->where('organization_id', $task->organization_id)
            ->whereDoesntHave('tasks', fn ($q) => $q->where('tasks.id', $task->id));
    }

    /**
     * One query for every folder in the company, then an in-memory walk —
     * shared by the document picker (TaskDocumentController) and the
     * folder picker (TaskFolderController) for their breadcrumb-style
     * "Marketing / Q3" display, never one query per folder.
     *
     * @return array<int, string>
     */
    public static function pathsFor(int $organizationId): array
    {
        $folders = static::where('organization_id', $organizationId)->get(['id', 'parent_id', 'name']);
        $byId = $folders->keyBy('id');

        $paths = [];
        foreach ($folders as $folder) {
            $segments = [];
            for ($cursor = $folder; $cursor !== null; $cursor = $cursor->parent_id !== null ? $byId->get($cursor->parent_id) : null) {
                array_unshift($segments, $cursor->name);
            }
            $paths[$folder->id] = implode(' / ', $segments);
        }

        return $paths;
    }
}
