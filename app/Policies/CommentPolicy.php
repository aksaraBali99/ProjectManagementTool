<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->hasPermission('view_comments', $comment->task->organization_id);
    }

    /**
     * No Comment/Task argument to derive an organization from (the
     * controller calls this as a blanket check, with the real scoping done
     * separately via Gate::authorize('view', $task) before this). Checking
     * hasPermission() without an org here would only see global roles and
     * incorrectly reject every per-org-role user (staff/management/client
     * all hold add_edit_own_comment only via their org-specific role, not
     * a global one) — so this stays unconditional true, same reasoning as
     * ProjectPolicy::viewAny.
     */
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Comment $comment): bool
    {
        return $this->canEditOwnComments($user, $comment->task->organization_id)
            ? ($user->isSuperAdmin() || $user->isOwner() || $comment->user_id === $user->id)
            : false;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $this->update($user, $comment);
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
