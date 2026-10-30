<?php

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use App\Models\AuditLog;
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
 * created/updated, before Aug 2026), which stored only the new value,
 * flat. Those only ever logged a real change, never a no-op.
 *
 * Falls back to updated_at when there is no candidate: the task has no
 * audit rows (TaskObserver logs nothing without a signed-in user), or its
 * logged history ends with it leaving Completed, so it was completed again
 * without a log. What can NOT be detected: a logged completion followed by
 * an unlogged reopen and an unlogged re-completion — the history then
 * still ends on the logged completion, and that date is used. Every status
 * change in the app runs as a signed-in user, so this needs a console or
 * raw write. A task with no audit rows and no updated_at or created_at
 * either is left empty and counted on its own line.
 *
 * Writes go through the query builder: no model events, so no per-task
 * audit rows or notifications, and updated_at stays as it was. A real run
 * that writes anything adds ONE summary audit row
 * (tasks.completed_at_backfilled), like solva:bootstrap --reset-owner's
 * CLI entry: no user, and no company unless --organization was given.
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
        'legacy_status_change' => 'a pre-observer status change (before Aug 2026)',
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
        $noDate = 0;

        $completed()
            ->when(! $force, fn ($query) => $query->whereNull('completed_at'))
            ->select(['id', 'completed_at', 'updated_at', 'created_at'])
            ->chunkById(self::CHUNK, function (Collection $tasks) use (&$counts, &$unchanged, &$noDate, $dryRun) {
                $history = $this->statusHistory($tasks->pluck('id'));
                $writes = [];

                foreach ($tasks as $task) {
                    [$completedAt, $source] = self::lastCompletion($history->get($task->id, collect()))
                        ?? [$task->updated_at ?? $task->created_at, 'fallback'];

                    // Nothing to go on: leave it empty rather than report
                    // it as filled.
                    if ($completedAt === null) {
                        $noDate++;

                        continue;
                    }

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
        $rows[] = ['Left empty, no date available', $noDate];
        $rows[] = ['Skipped, already set', $skipped];
        if ($force) {
            $rows[] = ['Already correct (--force)', $unchanged];
        }
        $rows[] = [$dryRun ? 'Total that would be written' : 'Total written', $written];

        $this->table(['', 'Tasks'], $rows);

        if (! $dryRun && $written > 0) {
            $this->logRun($organizationId, $force, $checked, $counts, $noDate, $skipped, $written);
        }
        $this->line(sprintf('Took %.2fs.', microtime(true) - $startedAt));

        return self::SUCCESS;
    }

    /**
     * One audit row per run that wrote something, so the Audit Trail shows
     * when and how completion dates appeared. Flat keys, so the trail's
     * describeChanges() reads "Tasks Written: 878; …" rather than JSON.
     * AuditLog::create directly, not through an observer: no notification.
     *
     * @param  array<string, int>  $counts
     */
    private function logRun(?int $organizationId, bool $force, int $checked, array $counts, int $noDate, int $skipped, int $written): void
    {
        AuditLog::create([
            'organization_id' => $organizationId,
            'user_id' => null,
            'action' => 'tasks.completed_at_backfilled',
            'entity_type' => 'command',
            'entity_id' => 0,
            'changes' => [
                'source' => 'cli_backfill',
                'triggered_from' => 'tasks:backfill-completed-at'.($force ? ' --force' : ''),
                'tasks_checked' => $checked,
                'tasks_written' => $written,
                'from_status_changed' => $counts['status_changed'],
                'from_other_status_change' => $counts['other_status_change'],
                'from_created' => $counts['created'],
                'from_legacy_status_change' => $counts['legacy_status_change'],
                'from_updated_at_fallback' => $counts['fallback'],
                'left_empty' => $noDate,
                'skipped_already_set' => $skipped,
            ],
        ]);
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
                $source = in_array($row->action, ['task.created', 'created'], true) ? 'created' : 'legacy_status_change';
            }

            $last = $isCompletion ? [(string) $row->created_at, $source] : null;
        }

        return $last;
    }
}
