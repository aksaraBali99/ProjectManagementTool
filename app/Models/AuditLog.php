<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['organization_id', 'user_id', 'action', 'entity_type', 'entity_id', 'import_batch_id', 'changes'])]
class AuditLog extends Model
{
    protected $table = 'audit_log';

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Null for the overwhelming majority of rows — only set on entries
     * produced by a bulk Import commit, matched by the UUID string stored
     * in both this column and changes['import_batch_id'].
     */
    public function importBatch(): BelongsTo
    {
        return $this->belongsTo(ImportBatch::class, 'import_batch_id', 'uuid');
    }

    /**
     * Which specific record this entry is about, e.g. a task's title —
     * entity_type/entity_id alone (a string plus a raw number) don't tell
     * an admin WHICH task/subtask/comment changed. Falls back to a
     * "#id (deleted)" placeholder for a hard-deleted subtask/comment
     * (Task only soft-deletes, so it's always still findable).
     */
    public function entityLabel(): string
    {
        $record = match ($this->entity_type) {
            'task' => Task::withTrashed()->find($this->entity_id),
            'subtask' => Subtask::find($this->entity_id),
            'comment' => Comment::find($this->entity_id),
            default => null,
        };

        if (! $record) {
            return ucfirst($this->entity_type)." #{$this->entity_id} (deleted)";
        }

        return match ($this->entity_type) {
            'task', 'subtask' => $record->title,
            'comment' => Str::limit($record->body, 40),
            default => "{$this->entity_type} #{$this->entity_id}",
        };
    }

    /**
     * Where a notification about this entry should link to — always the
     * parent task's edit page, since that's the one page every entity
     * type (task, subtask, comment) is reachable from. Null if the
     * underlying task can no longer be resolved.
     */
    public function linkUrl(): ?string
    {
        $taskId = match ($this->entity_type) {
            'task' => $this->entity_id,
            'subtask' => Subtask::find($this->entity_id)?->task_id,
            'comment' => Comment::find($this->entity_id)?->task_id,
            default => null,
        };

        return $taskId ? route('tasks.edit', $taskId) : null;
    }

    /**
     * A human-readable rendering of `action`, e.g. 'task.status_changed'
     * becomes "Task Status Changed" — the raw dotted slug stays as the
     * filter dropdown's value (still clear enough there), but the table
     * itself should read as prose, not a code identifier.
     */
    public function actionLabel(): string
    {
        return Str::headline(str_replace('.', ' ', $this->action));
    }

    /**
     * A human-readable rendering of `changes`, e.g. "Status changed from
     * Pending to In progress" — never raw JSON on screen. `changes` takes
     * one of two shapes depending on the action: an update-style diff
     * (every value is ['old' => ..., 'new' => ...], from an Observer's
     * updated() hook) or a flat snapshot (from created()/deleted(), where
     * there's no "old" to compare against).
     *
     * Reads via getAttribute('changes') rather than $this->changes:
     * Eloquent's own HasAttributes trait declares an internal protected
     * $changes property (its dirty-tracking state from the last save),
     * which shadows this model's "changes" database column when accessed
     * as a property from inside the model's own methods — getAttribute()
     * goes through the real attribute/cast pipeline regardless.
     */
    public function describeChanges(): string
    {
        $changes = $this->getAttribute('changes') ?? [];

        if (empty($changes)) {
            return match (true) {
                str_ends_with($this->action, '.deactivated') => 'Deactivated.',
                str_ends_with($this->action, '.reactivated') => 'Reactivated.',
                default => '—',
            };
        }

        $isDiff = collect($changes)->every(
            fn ($value) => is_array($value) && array_key_exists('old', $value) && array_key_exists('new', $value)
        );

        if ($isDiff) {
            return collect($changes)
                ->map(fn ($diff, $field) => Str::headline($field).' changed from '
                    .$this->formatFieldValue($field, $diff['old']).' to '
                    .$this->formatFieldValue($field, $diff['new']))
                ->implode('; ');
        }

        return collect($changes)
            ->map(fn ($value, $field) => Str::headline($field).': '.$this->formatFieldValue($field, $value))
            ->implode('; ');
    }

    private function formatFieldValue(string $field, mixed $value): string
    {
        if ($value === null) {
            return $field === 'assignee_id' ? 'Unassigned' : '—';
        }

        return match ($field) {
            'status' => TaskStatus::tryFrom($value)?->label() ?? (string) $value,
            'priority' => Priority::tryFrom($value)?->label() ?? (string) $value,
            'assignee_id' => User::find($value)?->name ?? "User #{$value}",
            'is_done' => $value ? 'Done' : 'Not done',
            default => is_bool($value) ? ($value ? 'Yes' : 'No') : (string) $value,
        };
    }
}
