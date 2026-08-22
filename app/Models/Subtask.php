<?php

namespace App\Models;

use App\Observers\SubtaskObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['task_id', 'title', 'assignee_id', 'is_done', 'due_date', 'start_date'])]
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

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
