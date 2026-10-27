<?php

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * task #72 phase 6: fills tasks.completed_at for tasks that were already
 * Completed before TaskObserver started recording it. A command, not part
 * of the migration, so it can be dry-run, re-run and checked on its own.
 *
 * Rule, per task whose status is Completed now (inactive ones included):
 * walk its audit_log rows oldest first, tracking the latest status event —
 * a change INTO completed sets the candidate to that row's time, a change
 * to anything else clears it. Whatever is left is the last completion.
 * That covers every shape TaskObserver writes:
 *
 *   - task.status_changed / task.reassigned (and any other action):
 *     {"status": {"old": …, "new": …}} — matched by payload, not action
 *     name, since a save that also changed the assignee is logged as
 *     task.reassigned
 *   - task.created: FLAT {"status": "completed", …}
 *
 * plus the pre-observer rows (entity_type App\Models\Task, actions
 * created/updated), which stored only the new value, flat.
 *
 * No candidate (no audit rows — TaskObserver logs nothing without a
 * signed-in user — or a later unlogged change) falls back to updated_at.
 *
 * Writes go through the query builder: no model events, so no new audit
 * rows or notifications, and updated_at stays as it was.
 */
class BackfillTaskCompletedAt extends Command
{
    protected $signature = 'tasks:backfill-completed-at
        {--dry-run : Change nothing, only report what would be written}
        {--organization= : Only this company (organization id)}
        {--force : Also recompute tasks that already have a completed_at}';

    protected $description = 'Fill tasks.completed_at for completed tasks from their audit history (task #72 phase 6)';

    private const CHUNK = 500;

    private const SOURCES = [
        'status_changed' => 'task.status_changed',
        'other_status_change' => 'task.reassigned or another action carrying a status change',
        'created' => 'task.created',
        'fallback' => 'the updated_at fallback',
    ];

    public function handle(): int
    {
        $organizationId = $this->option('organization');
        if ($organizationId !== null) {
            if (! ctype_digit((string) $organizationId) || ! DB::table('organizations')->where('id', $organizationId)->exists()) {
                $this->components->error("No company with id \"{$organizationId}\".");

                return self::FAILURE;
            }
            $organizationId = (int) $organizationId;
        }

        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        $startedAt = microtime(true);

        // DB::table, not Task: no organization scope, and soft-deleted
        // (inactive) tasks are included.
        $completed = fn () => DB::table('tasks')
            ->where('status', TaskStatus::Completed->value)
            ->when($organizationId, fn ($query) => $query->where('organization_id', $organizationId));

        $counts = array_fill_keys(array_keys(self::SOURCES), 0);
        $checked = $completed()->count();
        $skipped = $force ? 0 : $completed()->whereNotNull('completed_at')->count();
        $unchanged = 0;

        $completed()
            ->when(! $force, fn ($query) => $query->whereNull('completed_at'))
            ->select(['id', 'completed_at', 'updated_at', 'created_at'])
            ->chunkById(self::CHUNK, function (Collection $tasks) use (&$counts, &$unchanged, $dryRun) {
                $history = $this->statusHistory($tasks->pluck('id'));
                $writes = [];

                foreach ($tasks as $task) {
                    [$completedAt, $source] = self::lastCompletion($history->get($task->id, collect()))
                        ?? [$task->updated_at ?? $task->created_at, 'fallback'];

                    if ($task->completed_at !== null && (string) $task->completed_at === (string) $completedAt) {
                        $unchanged++;

                        continue;
                    }

                    $counts[$source]++;
                    $writes[$task->id] = $completedAt;

                    if ($this->output->isVerbose()) {
                        $this->line("  task #{$task->id}: {$completedAt} ({$source})");
                    }
                }

                if (! $dryRun && $writes !== []) {
                    DB::transaction(function () use ($writes) {
                        foreach ($writes as $id => $completedAt) {
                            DB::table('tasks')->where('id', $id)->update(['completed_at' => $completedAt]);
                        }
                    });
                }
            });

        $written = array_sum($counts);
        $verb = $dryRun ? 'Would fill' : 'Filled';

        if ($dryRun) {
            $this->components->warn('Dry run: nothing was written.');
        }

        $rows = [['Completed tasks checked', $checked]];
        foreach (self::SOURCES as $key => $label) {
            $rows[] = ["{$verb} from {$label}", $counts[$key]];
        }
        $rows[] = ['Skipped, already set', $skipped];
        if ($force) {
            $rows[] = ['Already correct (--force)', $unchanged];
        }
        $rows[] = [$dryRun ? 'Total that would be written' : 'Total written', $written];

        $this->table(['', 'Tasks'], $rows);
        $this->line(sprintf('Took %.2fs.', microtime(true) - $startedAt));

        return self::SUCCESS;
    }

    /**
     * The audit rows that mention status, for these tasks, oldest first.
     * The LIKE only narrows the read; every row is still decoded and
     * checked properly in lastCompletion().
     *
     * @param  Collection<int, int>  $taskIds
     * @return Collection<int, Collection<int, object>>
     */
    private function statusHistory(Collection $taskIds): Collection
    {
        return DB::table('audit_log')
            ->whereIn('entity_type', ['task', 'App\\Models\\Task'])
            ->whereIn('entity_id', $taskIds)
            ->where('changes', 'like', '%"status"%')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['entity_id', 'action', 'changes', 'created_at'])
            ->groupBy('entity_id');
    }

    /**
     * @param  Collection<int, object>  $rows  one task's audit rows, oldest first
     * @return array{0: string, 1: string}|null [time, source key], or null if the history ends without a completion
     */
    private static function lastCompletion(Collection $rows): ?array
    {
        $last = null;

        foreach ($rows as $row) {
            $changes = json_decode($row->changes, true);
            if (! is_array($changes) || ! array_key_exists('status', $changes)) {
                continue;
            }
            $status = $changes['status'];

            if (is_array($status)) {
                // {"old": …, "new": …}
                $isCompletion = ($status['new'] ?? null) === TaskStatus::Completed->value;
                $source = $row->action === 'task.status_changed' ? 'status_changed' : 'other_status_change';
            } else {
                // Flat: task.created, or a pre-observer created/updated row.
                $isCompletion = $status === TaskStatus::Completed->value;
                $source = in_array($row->action, ['task.created', 'created'], true) ? 'created' : 'other_status_change';
            }

            $last = $isCompletion ? [(string) $row->created_at, $source] : null;
        }

        return $last;
    }
}
