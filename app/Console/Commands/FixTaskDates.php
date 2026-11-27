<?php

namespace App\Console\Commands;

use App\Models\Subtask;
use App\Models\User;
use App\Services\TaskDateFixer;
use App\Support\TaskDateIssues;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

/**
 * SOL-0022: corrects existing tasks and subtasks that break the date rules
 * (what solava:check-task-dates lists). See TaskDateFixer for what changes
 * and in which order.
 *
 * A dry run by default: it prints every change it would make and writes
 * nothing. --apply makes them, signed in as the --as user (an active owner
 * or super admin), so the Audit Trail shows each change as theirs: the
 * observers write no audit row without a signed-in user. Notifications are
 * held back while it runs ('datafix.running', TagsImportBatch). One
 * transaction per task; a task that fails is reported and the rest carry
 * on. A CSV of what changed goes to storage/app/datafix.
 *
 * Safe to re-run: it only finds what still breaks the rules. New rows can
 * appear later by design (a task started late), so it's meant to be run
 * again from time to time.
 */
class FixTaskDates extends Command
{
    protected $signature = 'solava:fix-task-dates
        {--apply : Make the changes. Without it, only show them}
        {--as= : Username or email of the owner or super admin the changes are made as (required with --apply)}';

    protected $description = 'Correct tasks and subtasks whose dates break the due date rules (dry run unless --apply)';

    private const COLUMNS = ['Company', 'Task', 'Subtask', 'Field', 'Old', 'New', 'Reason'];

    public function handle(TaskDateFixer $fixer): int
    {
        $apply = (bool) $this->option('apply');
        $user = null;
        if ($apply) {
            $user = $this->resolveUser();
            if ($user === null) {
                return self::FAILURE;
            }
        }

        foreach (TaskDateIssues::subtasksWithoutTask()->orderBy('id')->get() as $orphan) {
            /** @var Subtask $orphan */
            $this->warn("Skipped: subtask #{$orphan->id} \"{$orphan->title}\" has no task (task #{$orphan->task_id}).");
        }

        $tasks = $fixer->tasksToFix();
        $planned = $tasks->flatMap(fn ($task) => $fixer->plan($task))->values()->all();

        if ($planned === []) {
            $this->info('Nothing to fix.');

            return self::SUCCESS;
        }

        if (! $apply) {
            $this->table(self::COLUMNS, $this->rows($planned));
            $this->line($this->totals($planned));
            $this->info('Dry run: nothing changed. Run again with --apply --as=<username> to make these changes.');

            return self::SUCCESS;
        }

        $applied = [];
        $failed = [];
        $previousUser = Auth::user();
        Auth::setUser($user);
        app()->instance('datafix.running', true);

        try {
            foreach ($tasks as $task) {
                try {
                    array_push($applied, ...$fixer->apply($task));
                } catch (Throwable $e) {
                    $failed[] = $task->code ?? "#{$task->id}";
                    $this->error('Failed: '.($task->code ?? "task #{$task->id}").': '.$e->getMessage());
                }
            }
        } finally {
            app()->forgetInstance('datafix.running');
            if ($previousUser) {
                Auth::setUser($previousUser);
            } else {
                Auth::forgetUser();
            }
        }

        if ($applied !== []) {
            $this->table(self::COLUMNS, $this->rows($applied));
            $this->line($this->totals($applied));
            $this->reportChanges($applied, $user);
        }

        if ($failed !== []) {
            $this->error(count($failed).' '.str('task')->plural(count($failed)).' failed and were left unchanged: '.implode(', ', $failed).'.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /** The --as user, or null after saying why it can't be used. */
    private function resolveUser(): ?User
    {
        $as = trim((string) $this->option('as'));
        if ($as === '') {
            $this->error('--apply needs --as=<username or email>: the owner or super admin the changes are made as, so the Audit Trail shows who made them.');

            return null;
        }

        // Username first: one user's email could be another's username.
        $user = User::where('username', $as)->first() ?? User::where('email', $as)->first();
        if ($user === null) {
            $this->error("No user with the username or email \"{$as}\".");

            return null;
        }
        if (! $user->is_active) {
            $this->error("{$user->username} is inactive.");

            return null;
        }
        if (! $user->isOwner() && ! $user->isSuperAdmin()) {
            $this->error("{$user->username} isn't an owner or super admin.");

            return null;
        }

        return $user;
    }

    /**
     * @param  list<array<string, ?string>>  $fixes
     * @return list<list<?string>>
     */
    private function rows(array $fixes): array
    {
        return array_map(fn (array $fix) => [
            $fix['company'], $fix['task'], $fix['subtask'], $fix['field'], $fix['old'], $fix['new'], $fix['reason'],
        ], $fixes);
    }

    /** @param  list<array<string, ?string>>  $fixes */
    private function totals(array $fixes): string
    {
        $tasks = count(array_unique(array_column($fixes, 'task')));
        $subtaskChanges = count(array_filter($fixes, fn (array $fix) => $fix['subtask'] !== '—'));
        $taskChanges = count($fixes) - $subtaskChanges;

        return count($fixes).' '.str('change')->plural(count($fixes))." on {$tasks} ".str('task')->plural($tasks)
            .": {$subtaskChanges} subtask due ".str('date')->plural($subtaskChanges).", {$taskChanges} task due ".str('date')->plural($taskChanges).'.';
    }

    /**
     * The CSV of what changed, written after the changes (and their table)
     * so a report that can't be written never hides what was done.
     *
     * @param  list<array<string, ?string>>  $fixes
     */
    private function reportChanges(array $fixes, User $user): void
    {
        try {
            $this->info('Report: '.$this->writeReport($fixes, $user));
        } catch (Throwable $e) {
            $this->warn('The changes above were made, but the CSV report couldn\'t be written: '.$e->getMessage());
        }
    }

    /** @param  list<array<string, ?string>>  $fixes */
    private function writeReport(array $fixes, User $user): string
    {
        $directory = storage_path('app'.DIRECTORY_SEPARATOR.'datafix');
        File::ensureDirectoryExists($directory);
        $appliedAt = now();
        $path = $directory.DIRECTORY_SEPARATOR.'fix-task-dates-'.$appliedAt->format('Y-m-d_His').'.csv';

        $handle = @fopen($path, 'w');
        if ($handle === false) {
            throw new RuntimeException("can't open {$path} for writing");
        }
        fputcsv($handle, ['Applied at', 'As', ...self::COLUMNS]);
        foreach ($fixes as $fix) {
            fputcsv($handle, [
                $appliedAt->toDateTimeString(), $user->username,
                $fix['company'], $fix['task'], $fix['subtask'], $fix['field'], $fix['old'], $fix['new'], $fix['reason'],
            ]);
        }
        fclose($handle);

        return $path;
    }
}
