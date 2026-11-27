<?php

namespace App\Support;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

/**
 * SOL-0022: the rows that break the date rules, found the same way by
 * solava:check-task-dates (which lists them) and solava:fix-task-dates
 * (which corrects them). Every company, deactivated tasks included, so a
 * task activated later is already right.
 */
class TaskDateIssues
{
    // Dates are compared with DATE(): MySQL stores a date column as a date,
    // but SQLite (tests, local copies) keeps the text it was given, so the
    // same day can be "2026-11-27" and "2026-11-27 00:00:00".

    /** Tasks whose due date is earlier than their start date. */
    public static function tasksDueBeforeStart(): Builder
    {
        return self::allTasks()
            ->whereNotNull('start_date')->whereNotNull('due_date')
            ->whereRaw('DATE(due_date) < DATE(start_date)');
    }

    /** Subtasks whose due date is earlier than their start date. */
    public static function subtasksDueBeforeStart(): Builder
    {
        return Subtask::query()
            ->whereNotNull('start_date')->whereNotNull('due_date')
            ->whereRaw('DATE(due_date) < DATE(start_date)');
    }

    /**
     * Tasks with a due date and at least one subtask due after it. A task
     * with no due date has no limit (TaskDueDateExtension).
     */
    public static function tasksWithSubtaskDueAfter(): Builder
    {
        return self::allTasks()
            ->whereNotNull('tasks.due_date')
            ->whereExists(fn ($query) => $query->selectRaw('1')->from('subtasks')
                ->whereColumn('subtasks.task_id', 'tasks.id')
                ->whereRaw('DATE(subtasks.due_date) > DATE(tasks.due_date)'));
    }

    /** Subtasks whose task row is gone (shouldn't happen): reported, never fixed. */
    public static function subtasksWithoutTask(): Builder
    {
        return Subtask::query()->whereNotExists(fn ($query) => $query->selectRaw('1')->from('tasks')
            ->whereColumn('tasks.id', 'subtasks.task_id'));
    }

    /** Every task, in every company, deactivated ones included. */
    public static function allTasks(): Builder
    {
        return Task::withoutGlobalScopes()->withTrashed();
    }
}
