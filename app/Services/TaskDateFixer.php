<?php

namespace App\Services;

use App\Models\Subtask;
use App\Models\Task;
use App\Support\DateRange;
use App\Support\TaskDateIssues;
use App\Support\TaskDueDateExtension;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * SOL-0022: corrects existing tasks and subtasks that break the date rules
 * (solava:fix-task-dates). Per task, in this order, since each step can
 * create work for the next:
 *
 *  1. a subtask due before its start date: due date = its start date
 *  2. the task due before its start date: due date = its start date
 *  3. the task due before one of its subtasks: due date = its latest
 *     subtask's (TaskDueDateExtension::extend(), the same as confirming
 *     in the app). Only moves the due date later, so it can't undo 2.
 *
 * Only due dates change, and only to a later date; nothing is cleared. A
 * task with no due date has no limit and isn't given one. A subtask
 * starting before its task is left alone (the app only notes it).
 *
 * plan() works out the changes without saving; apply() saves them through
 * the models, so the observers write the audit log, and returns the same
 * list.
 *
 * @phpstan-type DateFix array{company: ?string, task: string, subtask: string, field: string, old: string, new: string, reason: string}
 */
class TaskDateFixer
{
    public const REASON_DUE_BEFORE_START = 'Due date was before the start date';

    public const REASON_SUBTASK_DUE_AFTER = 'A subtask is due after the task';

    /**
     * Owner decision D1: when the due date is before the start date, the
     * due date moves to the start date and the start date stays. Most such
     * rows come from a task started late (TaskObserver::saving() sets the
     * start to today), so the start date is the true one.
     */
    public static function correctedDueDate(string $startDate, string $dueDate): string
    {
        return $startDate;
    }

    /**
     * Every task with something to fix, with its company and subtasks
     * loaded: every company, deactivated tasks included.
     *
     * @return Collection<int, Task>
     */
    public function tasksToFix(): Collection
    {
        $taskIds = TaskDateIssues::tasksDueBeforeStart()->pluck('id')
            ->merge(TaskDateIssues::subtasksDueBeforeStart()->pluck('task_id'))
            ->merge(TaskDateIssues::tasksWithSubtaskDueAfter()->pluck('id'))
            ->unique()->values();

        return TaskDateIssues::allTasks()
            ->whereIn('id', $taskIds)
            ->with(['organization:id,name,code', 'subtasks' => fn ($query) => $query->orderBy('id')])
            ->orderBy('organization_id')->orderBy('task_number')
            ->get();
    }

    /**
     * The changes this task needs, in order, without saving anything.
     *
     * @return list<DateFix>
     */
    public function plan(Task $task): array
    {
        $fixes = [];
        $subtaskDueDates = [];

        foreach ($task->subtasks as $subtask) {
            $start = DateRange::normalize($subtask->start_date);
            $due = DateRange::normalize($subtask->due_date);
            if (DateRange::dueBeforeStart($start, $due)) {
                $corrected = self::correctedDueDate($start, $due);
                $fixes[] = $this->fix($task, $subtask, $due, $corrected, self::REASON_DUE_BEFORE_START);
                $due = $corrected;
            }
            $subtaskDueDates[] = $due;
        }

        $taskStart = DateRange::normalize($task->start_date);
        $taskDue = DateRange::normalize($task->due_date);
        if (DateRange::dueBeforeStart($taskStart, $taskDue)) {
            $corrected = self::correctedDueDate($taskStart, $taskDue);
            $fixes[] = $this->fix($task, null, $taskDue, $corrected, self::REASON_DUE_BEFORE_START);
            $taskDue = $corrected;
        }

        $latestSubtaskDue = DateRange::latest($subtaskDueDates);
        if ($taskDue !== null && $latestSubtaskDue !== null && $latestSubtaskDue > $taskDue) {
            $fixes[] = $this->fix($task, null, $taskDue, $latestSubtaskDue, self::REASON_SUBTASK_DUE_AFTER);
        }

        return $fixes;
    }

    /**
     * Saves this task's changes in one transaction and returns them. The
     * caller signs in a user first, or the observers write no audit log.
     *
     * @return list<DateFix>
     */
    public function apply(Task $task): array
    {
        $fixes = $this->plan($task);
        if ($fixes === []) {
            return [];
        }

        DB::transaction(function () use ($task) {
            foreach ($task->subtasks as $subtask) {
                $start = DateRange::normalize($subtask->start_date);
                $due = DateRange::normalize($subtask->due_date);
                if (DateRange::dueBeforeStart($start, $due)) {
                    // SubtaskObserver reads the task for the audit row's
                    // company; a deactivated one wouldn't load on its own.
                    $subtask->setRelation('task', $task);
                    $subtask->update(['due_date' => self::correctedDueDate($start, $due)]);
                }
            }

            $start = DateRange::normalize($task->start_date);
            $due = DateRange::normalize($task->due_date);
            if (DateRange::dueBeforeStart($start, $due)) {
                $task->update(['due_date' => self::correctedDueDate($start, $due)]);
            }

            TaskDueDateExtension::extend($task);
        });

        return $fixes;
    }

    /** @return DateFix */
    private function fix(Task $task, ?Subtask $subtask, string $old, string $new, string $reason): array
    {
        return [
            'company' => $task->organization?->name,
            'task' => ($task->code ?? "#{$task->id}").($task->trashed() ? ' (inactive)' : ''),
            'subtask' => $subtask?->title ?? '—',
            'field' => 'due_date',
            'old' => $old,
            'new' => $new,
            'reason' => $reason,
        ];
    }
}
