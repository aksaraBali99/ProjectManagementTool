<?php

namespace App\Models;

use App\Observers\CommentObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['task_id', 'user_id', 'body'])]
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
