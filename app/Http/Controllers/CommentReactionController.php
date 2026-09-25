<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * task #70 phase 4: emoji reactions on comments and replies.
 *
 * Permission is the same rule as commenting itself — anyone who can view
 * the task can react, no additional restriction (CLAUDE.md's role model:
 * staff with department access, management, client, owner/super_admin) —
 * enforced here server-side via Gate::authorize('view', ...), never left
 * to the client hiding the picker.
 *
 * Deliberately no notification of any kind is fired here — reactions are
 * high-frequency and low-stakes, unlike a comment, reply, or @mention,
 * which is why this doesn't reuse MentionedInCommentNotification or add a
 * new notification class at all.
 */
class CommentReactionController extends Controller
{
    /**
     * One reaction per person per comment (comment_reactions' own
     * unique(comment_id, user_id) constraint backs this up at the DB
     * level too, not just here):
     * - no existing reaction from this user on this comment -> create one.
     * - an existing reaction with a DIFFERENT emoji -> replace it in place
     *   (updateOrCreate on the same unique key updates the same row, never
     *   inserts a second one).
     * - an existing reaction with the SAME emoji -> remove it (toggle off).
     * Applies identically whether $comment is itself top-level or a reply
     * — nothing here distinguishes the two.
     */
    public function store(Request $request, Comment $comment): JsonResponse
    {
        Gate::authorize('view', $comment->task);

        $data = $request->validate([
            'emoji' => ['required', 'string', 'min:1', 'max:32'],
        ]);

        $existing = CommentReaction::query()
            ->where('comment_id', $comment->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing && $existing->emoji === $data['emoji']) {
            $existing->delete();
        } else {
            CommentReaction::updateOrCreate(
                ['comment_id' => $comment->id, 'user_id' => auth()->id()],
                ['emoji' => $data['emoji']],
            );
        }

        $comment->load('reactions.user');

        return response()->json([
            'reactions' => $comment->reactionSummary(auth()->id()),
            'reactions_hash' => $comment->reactionsHash(auth()->id()),
        ]);
    }
}
