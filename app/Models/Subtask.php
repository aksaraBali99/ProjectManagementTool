<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTaskIncludingDeactivated;
use App\Observers\SubtaskObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['task_id', 'title', 'description', 'assignee_id', 'is_done', 'due_date', 'start_date'])]
#[ObservedBy(SubtaskObserver::class)]
class Subtask extends Model
{
    use BelongsToTaskIncludingDeactivated;

    /**
     * SOL-0022: the longest description a subtask can save, in characters
     * of the cleaned HTML (mb_strlen, after RichText::normalize()). The
     * column is TEXT (65,535 bytes), and 16,000 characters fit at up to 4
     * bytes each, so no migration is needed. For a longer limit, change
     * the column to mediumText first (expand-only).
     *
     * SubtaskController checks it on the raw request and again on the
     * cleaned HTML; StoreTaskRequest (Add Task) does the same per subtask.
     */
    public const MAX_DESCRIPTION_LENGTH = 16_000;

    public const TOO_LONG_MESSAGE = 'This description is too long to save.';

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

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
