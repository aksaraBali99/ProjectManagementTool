<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Support\DeactivatedTask;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

/**
 * task #123: every check reads the comment's task through parentTask
 * (deactivated tasks included), never task, which is null for a
 * deactivated task. A deactivated task's comments stay viewable, but none
 * can be added, edited or deleted.
 */
class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->hasPermission('view_comments', $comment->parentTask->organization_id);
    }

    /**
     * TC-55 (task #70): this used to take no Task and return an
     * unconditional true, on the reasoning that there was no organization
     * to resolve add_edit_own_comment against — but every caller already
     * had the Task in hand (CommentController::store() plus the five
     * comment-context upload endpoints), so the permission was simply
     * never enforced: a Staff user with "Add / edit own comments" revoked
     * could still post comments and replies, even though Edit/Delete
     * correctly disappeared (those go through update()/delete(), which
     * have always checked it via canEditOwnComments()).
     *
     * Now takes the Task and applies the SAME rule update() does, so
     * "can create" and "can edit my own" can't drift apart — posting a
     * comment you'd then be unable to edit was never a coherent state.
     * The separate Gate::authorize('view', $task) at each call site is
     * unchanged and still does the per-task scoping; this answers only
     * "may this user comment in that task's organization at all".
     */
    public function create(User $user, Task $task): bool|Response
    {
        return DeactivatedTask::denial($task)
            ?? $this->canEditOwnComments($user, $task->organization_id);
    }

    public function update(User $user, Comment $comment): bool|Response
    {
        $task = $comment->parentTask;

        if ($denied = DeactivatedTask::denial($task)) {
            return $denied;
        }

        return $this->canEditOwnComments($user, $task->organization_id)
            ? ($user->isSuperAdmin() || $user->isOwner() || $comment->user_id === $user->id)
            : false;
    }

    public function delete(User $user, Comment $comment): bool|Response
    {
        return $this->update($user, $comment);
    }

    /**
     * task #123: adding or removing a reaction (CommentReactionController)
     * needs the task viewable, and active.
     */
    public function react(User $user, Comment $comment): bool|Response
    {
        $task = $comment->parentTask;

        return DeactivatedTask::denial($task)
            ?? Gate::forUser($user)->allows('view', $task);
    }

    /**
     * The part of update()'s rule that DOESN'T depend on which specific
     * comment is being checked — only on the user and which organization
     * the comment's task belongs to. Pulled out so a caller rendering many
     * comments from the SAME task (CommentController::index(),
     * tasks/_comments.blade.php) can compute this once per request and
     * reuse it, rather than calling Gate::allows('update', $comment) fresh
     * per comment/reply — a real N+1 (isSuperAdmin()/isOwner()/
     * hasPermission() each ran their own query on every call, with no
     * memoization of their own), surfaced by task #70 phase 4's own
     * query-count regression test. Deliberately NOT cached on User itself
     * — a role/permission change and a re-check of it can legitimately
     * happen within the same PHP process without a real request boundary
     * between them (several existing tests do exactly this), so any cache
     * living longer than one controller method's own local scope risks
     * going stale.
     */
    public function canEditOwnComments(User $user, int $organizationId): bool
    {
        return $user->isSuperAdmin() || $user->isOwner() || $user->hasPermission('add_edit_own_comment', $organizationId);
    }
}
