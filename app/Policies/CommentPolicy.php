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
        if ($user->isSuperAdmin() || $user->isOwner()) {
            return true;
        }

        if (! $user->hasPermission('add_edit_own_comment', $comment->task->organization_id)) {
            return false;
        }

        return $comment->user_id === $user->id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $this->update($user, $comment);
    }
}
