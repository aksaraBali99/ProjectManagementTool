<?php

namespace App\Policies;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use App\Support\DeactivatedTask;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

/**
 * task #123: every check reads the subtask's task through parentTask
 * (deactivated tasks included), never task, which is null for a
 * deactivated task; and a deactivated task's subtasks can't be changed.
 */
class SubtaskPolicy
{
    public function create(User $user, Task $task): bool|Response
    {
        return DeactivatedTask::denial($task)
            ?? Gate::forUser($user)->allows('update', $task);
    }

    /**
     * Deliberately more permissive than update/delete: anyone who can view
     * the parent task may toggle a subtask's done state, even a staff
     * member who isn't the task's assignee and therefore can't edit the
     * task (or the subtask's title) at all. Gated by its own
     * toggle_subtask_done permission (currently granted to the same roles
     * as view_tasks, but a distinct slug so it can diverge later) on top
     * of the existing task-visibility scoping check.
     */
    public function toggle(User $user, Subtask $subtask): bool|Response
    {
        $task = $subtask->parentTask;

        return DeactivatedTask::denial($task)
            ?? ($user->hasPermission('toggle_subtask_done', $task->organization_id)
                && Gate::forUser($user)->allows('view', $task));
    }

    public function update(User $user, Subtask $subtask): bool|Response
    {
        $task = $subtask->parentTask;

        return DeactivatedTask::denial($task)
            ?? Gate::forUser($user)->allows('update', $task);
    }

    public function delete(User $user, Subtask $subtask): bool|Response
    {
        return $this->update($user, $subtask);
    }
}
