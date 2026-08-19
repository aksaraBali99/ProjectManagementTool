<?php

namespace App\Policies;

use App\Models\Subtask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class SubtaskPolicy
{
    public function create(User $user, Task $task): bool
    {
        return Gate::forUser($user)->allows('update', $task);
    }

    /**
     * Deliberately more permissive than update/delete: anyone who can view
     * the parent task may toggle a subtask's done state, even a staff
     * member who isn't the task's assignee and therefore can't edit the
     * task (or the subtask's title) at all.
     */
    public function toggle(User $user, Subtask $subtask): bool
    {
        return Gate::forUser($user)->allows('view', $subtask->task);
    }

    public function update(User $user, Subtask $subtask): bool
    {
        return Gate::forUser($user)->allows('update', $subtask->task);
    }

    public function delete(User $user, Subtask $subtask): bool
    {
        return $this->update($user, $subtask);
    }
}
