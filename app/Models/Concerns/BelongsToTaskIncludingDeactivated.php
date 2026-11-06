<?php

namespace App\Models\Concerns;

use App\Models\Task;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * task #123: for a model that belongs to a task (Subtask, Comment).
 */
trait BelongsToTaskIncludingDeactivated
{
    /**
     * The task, deactivated (soft-deleted) or not, for the policies: a
     * deactivated task still has to be checked (and refused) rather than
     * read as null. The model's own task() is left as it is, so every query
     * through it (e.g. TaskPolicy::viewAny()'s whereHas) keeps leaving
     * deactivated tasks out.
     */
    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id')->withTrashed();
    }
}
