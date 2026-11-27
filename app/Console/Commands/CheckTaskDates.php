<?php

namespace App\Console\Commands;

use App\Models\Subtask;
use App\Models\Task;
use App\Support\TaskDateIssues;
use Illuminate\Console\Command;

/**
 * SOL-0022: lists the tasks and subtasks that break the date rules, so the
 * owner can see how much existing data (from before the checks, Bulk
 * Import, or a task started late) solava:fix-task-dates would correct:
 * due dates earlier than start dates, and tasks due before one of their
 * subtasks.
 *
 * Read-only: it changes nothing and always exits 0. It skips the company
 * scope, so every company is listed, inactive tasks included (marked).
 */
class CheckTaskDates extends Command
{
    protected $signature = 'solava:check-task-dates';

    protected $description = 'List tasks and subtasks whose dates break the due date rules (read-only)';

    public function handle(): int
    {
        $tasks = TaskDateIssues::tasksDueBeforeStart()
            ->with('organization:id,name,code')
            ->orderBy('organization_id')->orderBy('task_number')
            ->get();

        $subtasks = TaskDateIssues::subtasksDueBeforeStart()
            ->with(['task' => fn ($query) => $query->withoutGlobalScopes()->withTrashed()->with('organization:id,name,code')])
            ->orderBy('task_id')->orderBy('id')
            ->get();

        $dueBeforeSubtasks = TaskDateIssues::tasksWithSubtaskDueAfter()
            ->with('organization:id,name,code')
            ->withMax('subtasks', 'due_date')
            ->orderBy('organization_id')->orderBy('task_number')
            ->get();

        if ($tasks->isEmpty() && $subtasks->isEmpty() && $dueBeforeSubtasks->isEmpty()) {
            $this->info('No task or subtask breaks the due date rules.');

            return self::SUCCESS;
        }

        $this->line("Tasks with a due date earlier than the start date: {$tasks->count()}");
        if ($tasks->isNotEmpty()) {
            $this->table(['Task', 'Title', 'Company', 'Start', 'Due'], $tasks->map(fn (Task $task) => [
                $this->taskLabel($task),
                $task->title,
                $task->organization?->name,
                $task->start_date->format('Y-m-d'),
                $task->due_date->format('Y-m-d'),
            ]));
        }

        $this->line("Subtasks with a due date earlier than the start date: {$subtasks->count()}");
        if ($subtasks->isNotEmpty()) {
            $this->table(['Task', 'Subtask', 'Company', 'Start', 'Due'], $subtasks->map(fn (Subtask $subtask) => [
                $subtask->task?->code ?? "#{$subtask->task_id}",
                $subtask->title,
                $subtask->task?->organization?->name,
                $subtask->start_date->format('Y-m-d'),
                $subtask->due_date->format('Y-m-d'),
            ]));
        }

        $this->line("Tasks due before one of their subtasks: {$dueBeforeSubtasks->count()}");
        if ($dueBeforeSubtasks->isNotEmpty()) {
            $this->table(['Task', 'Title', 'Company', 'Due', 'Latest subtask due'], $dueBeforeSubtasks->map(fn (Task $task) => [
                $this->taskLabel($task),
                $task->title,
                $task->organization?->name,
                $task->due_date->format('Y-m-d'),
                substr((string) $task->subtasks_max_due_date, 0, 10),
            ]));
        }

        return self::SUCCESS;
    }

    private function taskLabel(Task $task): string
    {
        return ($task->code ?? "#{$task->id}").($task->trashed() ? ' (inactive)' : '');
    }
}
