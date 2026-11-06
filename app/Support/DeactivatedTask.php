<?php

namespace App\Support;

use App\Models\Task;
use Illuminate\Auth\Access\Response;

/**
 * task #123: a deactivated (soft-deleted) task is archived: it can be
 * viewed, and the only action allowed on it is Activate
 * (TaskPolicy::delete(), unchanged). Every policy that allows a change to
 * a task, its subtasks, comments, reactions or linked documents checks
 * this first, so they all refuse with the same message.
 */
final class DeactivatedTask
{
    public const MESSAGE = 'This task is deactivated. Activate it to make changes.';

    /** The refusal for a deactivated task, or null when it's active. */
    public static function denial(?Task $task): ?Response
    {
        return $task?->trashed() ? Response::deny(self::MESSAGE) : null;
    }

    /**
     * Whether the task accepts changes at all: the UI flags that have no
     * policy of their own (react, create a new document from the task,
     * the subtask checkbox) ask this, never a bare trashed().
     */
    public static function allowsChanges(Task $task): bool
    {
        return self::denial($task) === null;
    }

    /**
     * For a controller check outside a policy: throws the same 403
     * (AuthorizationException) a policy refusal would.
     */
    public static function refuse(?Task $task): void
    {
        self::denial($task)?->authorize();
    }
}
