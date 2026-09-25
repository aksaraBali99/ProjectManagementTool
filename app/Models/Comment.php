<?php

namespace App\Models;

use App\Observers\CommentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['task_id', 'user_id', 'parent_comment_id', 'body'])]
#[ObservedBy(CommentObserver::class)]
class Comment extends Model
{
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The top-level comment this one replies to, or null for a top-level
     * comment itself. Threading is capped at one level —
     * CommentController::store() enforces server-side that a reply's own
     * parent_comment_id must point at a comment that is ITSELF top-level
     * (parentComment on that row is null), so this relation is never more
     * than one hop from a top-level comment.
     */
    public function parentComment(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }

    /**
     * A top-level comment's replies, oldest first — empty for a reply
     * itself, since replies can't be nested further. Task::comments()
     * still returns every row (top-level and replies alike, same table),
     * so callers that need the grouped shape do so explicitly (see
     * tasks/_comments.blade.php's topLevelComments/repliesByParent).
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_comment_id')->orderBy('created_at');
    }

    /**
     * Users @mentioned in this comment's body — kept as an explicit pivot
     * rather than parsed from the text on every read, so "was this person
     * already notified for this mention" survives edits (syncMentions()
     * in CommentController only notifies newly-attached rows).
     */
    public function mentionedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_mentions');
    }

    /**
     * task #70 phase 4: every emoji reaction on this comment, one row per
     * (user, comment) — CommentReactionController enforces that via the
     * comment_reactions table's unique(comment_id, user_id) constraint, so
     * a person always has at most one reaction here, never several.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class);
    }

    /**
     * Aggregated {emoji, count, user_names, reacted_by_me} for every
     * distinct emoji reacted with on this comment. Pure in-memory grouping
     * over an ALREADY EAGER-LOADED reactions.user relation — this method
     * never issues a query of its own. That's what keeps rendering an
     * entire task's comment list (however many comments/reactions it has)
     * at one aggregate query total for reactions, not a query per comment:
     * callers eager-load 'comments.reactions.user' once
     * (TaskManagementController, CommentController::index()) and this just
     * reshapes what's already in memory. $viewerId is a plain in-memory
     * comparison too — never a query of its own.
     */
    public function reactionSummary(?int $viewerId): array
    {
        return $this->reactions
            ->groupBy('emoji')
            ->map(fn ($group, $emoji) => [
                'emoji' => $emoji,
                'count' => $group->count(),
                'user_names' => $group->pluck('user.name')->all(),
                'reacted_by_me' => $viewerId !== null && $group->contains('user_id', $viewerId),
            ])
            ->values()
            ->all();
    }

    /**
     * A cheap fingerprint of reactionSummary(), for the 7s comment poll
     * (tasks/_comments.blade.php's syncComments()) to detect "did this
     * comment's reactions change" independently of "did its body change" —
     * the same body_hash idiom Phase 2/3 already established, extended
     * with its own hash rather than folded into body_hash, since a
     * reaction can change with the body completely untouched.
     */
    public function reactionsHash(?int $viewerId): string
    {
        return md5(json_encode($this->reactionSummary($viewerId)));
    }
}
