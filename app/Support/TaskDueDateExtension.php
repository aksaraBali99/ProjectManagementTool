<?php

namespace App\Support;

use App\Models\Task;

/**
 * SOL-0022: a subtask may be due after its task only if the task's due
 * date moves out to match, so a task is never due before its own work.
 * In the app the user confirms that first (the subtask endpoints and Add
 * Task refuse it without the extend_task_due_date flag); Bulk Import has
 * nobody to ask, so it extends and says so on the review page.
 *
 * A task with no due date has no limit and is never given one. Moving
 * the task's due date earlier than its latest subtask is refused instead
 * (UpdateTaskRequest), so the user decides which date gives way.
 */
class TaskDueDateExtension
{
    public const FLAG = 'extend_task_due_date';

    /**
     * The due date the task would need if one of its subtasks gets
     * $subtaskDue: the latest of that and its other subtasks' due dates,
     * or null when the task can stay as it is.
     */
    public static function requiredFor(Task $task, ?string $subtaskDue, ?int $exceptSubtaskId = null): ?string
    {
        $taskDue = DateRange::normalize($task->due_date);
        if ($taskDue === null || $subtaskDue === null || $subtaskDue <= $taskDue) {
            return null;
        }

        return DateRange::latest([$subtaskDue, self::latestSubtaskDue($task, $exceptSubtaskId)]);
    }

    /** The latest due date among the task's subtasks, or null when none has one. */
    public static function latestSubtaskDue(Task $task, ?int $exceptSubtaskId = null): ?string
    {
        $latest = $task->subtasks()
            ->when($exceptSubtaskId, fn ($query) => $query->whereKeyNot($exceptSubtaskId))
            ->max('due_date');

        return DateRange::normalize($latest);
    }

    /**
     * Moves the task's due date out to its latest subtask's, if that's
     * later. Returns the new date, or null when nothing changed. Saved
     * through the model, so TaskObserver writes the change to the audit
     * log (tagged with the import batch during an import).
     */
    public static function extend(Task $task): ?string
    {
        $taskDue = DateRange::normalize($task->due_date);
        $latest = self::latestSubtaskDue($task);

        if ($taskDue === null || $latest === null || $latest <= $taskDue) {
            return null;
        }

        $task->update(['due_date' => $latest]);

        return $latest;
    }

    public static function subtaskMessage(string $taskDue, string $newDue): string
    {
        return 'This subtask is due after its task ('.DateRange::format($taskDue).'). Continue to move the task\'s due date to '.DateRange::format($newDue).'.';
    }

    public static function newTaskMessage(string $taskDue, string $newDue): string
    {
        return 'A subtask is due after this task ('.DateRange::format($taskDue).'). Continue to move the task\'s due date to '.DateRange::format($newDue).'.';
    }

    public static function tooEarlyMessage(string $latestSubtaskDue): string
    {
        return 'Some subtasks are due after this date. Move them first, or choose '.DateRange::format($latestSubtaskDue).' or later.';
    }
}
