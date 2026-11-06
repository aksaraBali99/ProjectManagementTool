<?php

namespace App\Models;

use App\Observers\SubtaskObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['task_id', 'title', 'description', 'assignee_id', 'is_done', 'due_date', 'start_date'])]
#[ObservedBy(SubtaskObserver::class)]
class Subtask extends Model
{
    protected function casts(): array
    {
        return [
            'is_done' => 'boolean',
            // Explicit Y-m-d format (not just 'date') so JSON responses from
            // SubtaskController serialize a bare date the <input type="date">
            // rows can consume directly — the plain 'date' cast emits full
            // ISO8601 with a time component, which those inputs reject.
            'due_date' => 'date:Y-m-d',
            'start_date' => 'date:Y-m-d',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * task #123: the subtask's task, deactivated (soft-deleted) or not, for
     * the policies: a deactivated task still has to be checked (and
     * refused) rather than read as null. task() is left as it is, so
     * every query through it (e.g. TaskPolicy::viewAny()'s whereHas)
     * keeps leaving deactivated tasks out.
     */
    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id')->withTrashed();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
