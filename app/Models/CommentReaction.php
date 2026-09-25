<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['comment_id', 'user_id', 'emoji'])]
class CommentReaction extends Model
{
    // Only created_at exists on this table — a reaction that's replaced
    // with a different emoji (CommentReactionController::store()) updates
    // this same row's emoji column, not a separate updated_at.
    const UPDATED_AT = null;

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
