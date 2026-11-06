<?php

namespace App\Models;

use App\Enums\DocumentAccessLevel;
use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Support\RichText;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

#[Fillable(['organization_id', 'user_id', 'action', 'entity_type', 'entity_id', 'import_batch_id', 'changes'])]
class AuditLog extends Model
{
    protected $table = 'audit_log';

    const UPDATED_AT = null;

    /**
     * task #118 phase 4: set by primeEntityLabels() for a page of entries.
     * The entry's record (task, subtask or comment), false when it no
     * longer exists, or null when not primed (entityLabel() then looks it
     * up itself, as notifications do).
     */
    public Model|false|null $primedRecord = null;

    /**
     * task #118 phase 4: assignee names for describeChanges(), keyed by
     * user id, set by primeEntityLabels(); null when not primed.
     *
     * @var array<int, string>|null
     */
    public ?array $primedUserNames = null;

    /**
     * task #118 phase 4: folder names for describeChanges(), keyed by
     * folder id, set by primeEntityLabels(); null when not primed.
     *
     * @var array<int, string>|null
     */
    public ?array $primedFolderNames = null;

    /**
     * Bookkeeping keys stored alongside a change rather than describing a
     * field (an import adds 'source' => 'import' to every row it writes,
     * TagsImportBatch). Left out when deciding whether `changes` is an
     * old/new diff, and listed after it.
     */
    private const CHANGE_TAGS = ['source'];

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
     *
     * task #118 phase 4: a task is named by code and title (its
     * display_title, "BGS-0012 Fix invoice PDF layout"); a subtask or a
     * comment by its task's and its own ("BGS-0012 Fix invoice PDF layout
     * › Write tests", "BGS-0012 Fix invoice PDF layout › Looks good to
     * me"), or its own alone if the task is gone. The one place every
     * message about a task gets its name: the Audit Trail, the in-app
     * notification and the email's subject and body.
     */
    public function entityLabel(): string
    {
        // A console command's own run (e.g. tasks.completed_at_backfilled)
        // is about no single record: name the command instead.
        if ($this->entity_type === 'command') {
            return $this->getAttribute('changes')['triggered_from'] ?? 'Console command';
        }

        $record = $this->primedRecord !== null
            ? ($this->primedRecord ?: null)
            : match ($this->entity_type) {
                'task' => Task::withTrashed()->with('organization:id,code')->find($this->entity_id),
                'subtask' => Subtask::find($this->entity_id),
                'comment' => Comment::find($this->entity_id),
                default => null,
            };

        if (! $record) {
            return ucfirst($this->entity_type)." #{$this->entity_id} (deleted)";
        }

        $own = match ($this->entity_type) {
            'task' => $record->display_title,
            'subtask' => $record->title,
            'comment' => Str::limit(RichText::plainText($record->body), 40),
            default => "{$this->entity_type} #{$this->entity_id}",
        };

        $parent = in_array($this->entity_type, ['subtask', 'comment'], true) ? $this->parentTask($record) : null;

        return $parent === null ? $own : $parent->display_title.' › '.$own;
    }

    /** A subtask's or comment's task, deactivated ones included. */
    private function parentTask(Subtask|Comment $record): ?Task
    {
        return $record->relationLoaded('task')
            ? $record->task
            : Task::withTrashed()->with('organization:id,code')->find($record->task_id);
    }

    /**
     * task #118 phase 4: everything a page of Audit Trail entries needs
     * for entityLabel() and describeChanges(), in a fixed number of
     * queries however many entries there are: their tasks (deactivated
     * ones included) with their company codes, their subtasks and
     * comments with their tasks, and the assignees and folders their
     * changes name. Without it each entry looks these up on its own.
     *
     * @param  iterable<AuditLog>  $entries
     */
    public static function primeEntityLabels(iterable $entries): void
    {
        $entries = collect($entries);
        $idsOf = fn (string $type) => $entries->where('entity_type', $type)->pluck('entity_id')->unique()->values()->all();

        $records = [
            'task' => ($ids = $idsOf('task'))
                ? Task::withTrashed()->with('organization:id,code')->whereIn('id', $ids)->get()->keyBy('id')
                : collect(),
            'subtask' => ($ids = $idsOf('subtask'))
                ? Subtask::with(['task' => fn ($query) => $query->withTrashed()->with('organization:id,code')])->whereIn('id', $ids)->get()->keyBy('id')
                : collect(),
            'comment' => ($ids = $idsOf('comment'))
                ? Comment::with(['task' => fn ($query) => $query->withTrashed()->with('organization:id,code')])->whereIn('id', $ids)->get()->keyBy('id')
                : collect(),
        ];

        // An id field appears as a flat value (task.created) or as an
        // old/new pair (task.reassigned, document.moved).
        $idsIn = fn (string $field) => $entries
            ->flatMap(function (AuditLog $entry) use ($field) {
                $value = ($entry->getAttribute('changes') ?? [])[$field] ?? null;

                return is_array($value) ? [$value['old'] ?? null, $value['new'] ?? null] : [$value];
            })
            ->filter(fn ($id) => is_numeric($id))
            ->unique()
            ->values()
            ->all();

        $userNames = ($ids = $idsIn('assignee_id')) ? User::whereIn('id', $ids)->pluck('name', 'id')->all() : [];
        $folderNames = ($ids = $idsIn('folder_id')) ? DocumentFolder::whereIn('id', $ids)->pluck('name', 'id')->all() : [];

        foreach ($entries as $entry) {
            if (array_key_exists($entry->entity_type, $records)) {
                $entry->primedRecord = $records[$entry->entity_type]->get($entry->entity_id) ?? false;
            }
            $entry->primedUserNames = $userNames;
            $entry->primedFolderNames = $folderNames;
        }
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

        // An import's 'source' tag rides along with its diffs: without
        // setting it aside, an imported update would read as a snapshot and
        // each old/new pair would be formatted as one value.
        $tags = array_intersect_key($changes, array_flip(self::CHANGE_TAGS));
        $fields = array_diff_key($changes, $tags);

        $isDiff = $fields !== [] && collect($fields)->every(
            fn ($value) => is_array($value) && array_key_exists('old', $value) && array_key_exists('new', $value)
        );

        if ($isDiff) {
            return collect($fields)
                ->map(fn ($diff, $field) => $this->fieldLabel($field).' changed from '
                    .$this->formatFieldValue($field, $diff['old']).' to '
                    .$this->formatFieldValue($field, $diff['new']))
                ->merge(collect($tags)->map(fn ($value, $field) => $this->fieldLabel($field).': '.$this->formatFieldValue($field, $value)))
                ->implode('; ');
        }

        // task #118 phase 4: the task code leads ("Task code: BGS-0003;
        // Title: ..."), wherever the snapshot stored it.
        if (array_key_exists('task_number', $changes)) {
            $changes = ['task_number' => $changes['task_number']] + $changes;
        }

        return collect($changes)
            ->map(fn ($value, $field) => $this->fieldLabel($field).': '.$this->formatFieldValue($field, $value))
            ->implode('; ');
    }

    private function fieldLabel(string $field): string
    {
        return match ($field) {
            // task #118 phase 4: task.created's number, shown as the code.
            'task_number' => 'Task code',
            default => Str::headline($field),
        };
    }

    private function formatFieldValue(string $field, mixed $value): string
    {
        if ($value === null) {
            return match ($field) {
                'assignee_id' => 'Unassigned',
                // task #73 phase 2: document.moved's own from/to shape —
                // null folder_id means the company root, not "nothing".
                'folder_id' => 'Company root',
                default => '—',
            };
        }

        return match ($field) {
            'status' => TaskStatus::tryFrom($value)?->label() ?? (string) $value,
            'priority' => Priority::tryFrom($value)?->label() ?? (string) $value,
            'assignee_id' => ($this->primedUserNames !== null
                ? ($this->primedUserNames[$value] ?? null)
                : User::find($value)?->name) ?? "User #{$value}",
            // task #118 phase 4: the code in this entry's own company, as
            // it was when the task was created ("BGS-0003").
            'task_number' => Task::formatCode($this->organization?->code, (int) $value) ?? (string) $value,
            'is_done' => $value ? 'Done' : 'Not done',
            // task #73 phase 2: document.moved/document.access_level_changed.
            'folder_id' => ($this->primedFolderNames !== null
                ? ($this->primedFolderNames[$value] ?? null)
                : DocumentFolder::find($value)?->name) ?? "Folder #{$value}",
            'access_level' => DocumentAccessLevel::tryFrom($value)?->label() ?? (string) $value,
            // task #72 phase 6: the new value arrives as "Y-m-d H:i:s",
            // the old one as ISO-8601 (getOriginal() returns the cast
            // Carbon, which JSON-encodes that way). Same style as comment
            // timestamps either way.
            'completed_at' => rescue(fn () => Carbon::parse($value)->format('M j, Y g:ia'), (string) $value, false),
            // task #73 phase 2: document.deleted's own flat snapshot — an
            // array value would otherwise hit the generic (string) $value
            // cast below and fatal ("Array to string conversion").
            // Always empty this phase (delete is blocked outright while
            // any task is linked), but Phase 4 may populate it.
            'linked_task_ids' => empty($value) ? 'None' : implode(', ', array_map(fn ($id) => "#{$id}", (array) $value)),
            // Stored as editor HTML now — show the words, not the tags.
            'description', 'body' => RichText::plainText((string) $value) ?: '—',
            default => is_bool($value) ? ($value ? 'Yes' : 'No') : (is_array($value) ? implode(', ', $value) : (string) $value),
        };
    }
}
