<?php

namespace App\Console\Commands;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Console\Command;

/**
 * SOL-0022: lists tasks and subtasks whose due date is earlier than their
 * start date, so the owner can see how much existing data (from before
 * the check, Bulk Import, or a task started late) breaks the rule.
 *
 * Read-only: it changes nothing and always exits 0. It skips the company
 * scope, so every company is listed, inactive tasks included (marked).
 */
class CheckTaskDates extends Command
{
    protected $signature = 'solava:check-task-dates';

    protected $description = 'List tasks and subtasks whose due date is earlier than their start date (read-only)';

    public function handle(): int
    {
        $tasks = Task::withoutGlobalScopes()->withTrashed()
            ->with('organization:id,name,code')
            ->whereNotNull('start_date')->whereNotNull('due_date')
            ->whereColumn('due_date', '<', 'start_date')
            ->orderBy('organization_id')->orderBy('task_number')
            ->get();

        $subtasks = Subtask::query()
            ->with(['task' => fn ($query) => $query->withoutGlobalScopes()->withTrashed()->with('organization:id,name,code')])
            ->whereNotNull('start_date')->whereNotNull('due_date')
            ->whereColumn('due_date', '<', 'start_date')
            ->orderBy('task_id')->orderBy('id')
            ->get();

        if ($tasks->isEmpty() && $subtasks->isEmpty()) {
            $this->info('No task or subtask has a due date earlier than its start date.');

            return self::SUCCESS;
        }

        $this->line("Tasks with a due date earlier than the start date: {$tasks->count()}");
        if ($tasks->isNotEmpty()) {
            $this->table(['Task', 'Title', 'Company', 'Start', 'Due'], $tasks->map(fn (Task $task) => [
                ($task->code ?? "#{$task->id}").($task->trashed() ? ' (inactive)' : ''),
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

        return self::SUCCESS;
    }
}
