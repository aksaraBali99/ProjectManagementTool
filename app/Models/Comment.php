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
}
