<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionedInCommentNotification;
use App\Notifications\RepliedToCommentNotification;
use App\Support\RichText;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    private const MAX_BODY_TEXT_LENGTH = 2000;

    private const MAX_BODY_HTML_LENGTH = 100000;

    public function index(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        // A single flat fetch of every comment on this task — both
        // top-level and replies live in the same table/query. Grouping
        // (top-level + its replies nested underneath) happens client-side
        // in _comments.blade.php's syncComments(), same as the initial
        // page load groups the same shape server-side in the Blade
        // partial — current volume is small enough that this doesn't need
        // a more complex query strategy.
        $comments = $task->comments()->with('user')->orderBy('created_at')->get();

        return response()->json([
            'comments' => $comments->map(fn (Comment $comment) => [
                'id' => $comment->id,
                'parent_comment_id' => $comment->parent_comment_id,
                'body' => $comment->body,
                'body_html' => RichText::toHtml($comment->body),
                'body_hash' => md5($comment->body),
                'user_name' => $comment->user->name,
                'user_initials' => $comment->user->initials(),
                'user_avatar_bg' => $comment->user->avatarBackground(),
                'user_avatar_text' => $comment->user->avatarText(),
                'created_at' => $comment->created_at->format('M j, Y g:ia'),
                'can_edit' => Gate::allows('update', $comment),
            ])->values(),
        ]);
    }

    public function store(Request $request, Task $task): JsonResponse
    {
        // CommentPolicy@create doesn't take the task (it's a blanket "can
        // this user comment at all" check), so viewing the task itself —
        // the real scoping rule — is checked separately here.
        Gate::authorize('view', $task);
        Gate::authorize('create', Comment::class);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:500000'],
            'mentioned_user_ids' => ['sometimes', 'array'],
            'mentioned_user_ids.*' => ['integer'],
            'parent_comment_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        $parentCommentId = $this->resolveParentCommentId($task, $data['parent_comment_id'] ?? null);

        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'parent_comment_id' => $parentCommentId,
            'body' => $this->cleanBody($data['body']),
        ]);

        $this->syncMentions($comment, $task, $data['mentioned_user_ids'] ?? []);
        $this->notifyReply($comment);

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'parent_comment_id' => $comment->parent_comment_id,
                'body' => $comment->body,
                'body_html' => RichText::toHtml($comment->body),
                'body_hash' => md5($comment->body),
                'user_name' => auth()->user()->name,
                'user_initials' => auth()->user()->initials(),
                'user_avatar_bg' => auth()->user()->avatarBackground(),
                'user_avatar_text' => auth()->user()->avatarText(),
                'created_at' => $comment->created_at->format('M j, Y g:ia'),
            ],
        ], 201);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        Gate::authorize('update', $comment);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:500000'],
            'mentioned_user_ids' => ['sometimes', 'array'],
            'mentioned_user_ids.*' => ['integer'],
        ]);

        $comment->update(['body' => $this->cleanBody($data['body'])]);

        $this->syncMentions($comment, $comment->task, $data['mentioned_user_ids'] ?? []);

        return response()->json(['comment' => [
            'id' => $comment->id,
            'body' => $comment->body,
            'body_html' => RichText::toHtml($comment->body),
            'body_hash' => md5($comment->body),
        ]]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Enforces the one-level cap server-side, not just in the UI (only
     * the "Reply" action, shown on top-level comments alone, ever sends a
     * parent_comment_id in the first place, but a crafted request could
     * send any id): a reply's parent must belong to THIS task and must
     * itself be top-level (its own parent_comment_id is null) — never
     * another reply. Returns null for an ordinary top-level comment.
     */
    private function resolveParentCommentId(Task $task, ?int $parentCommentId): ?int
    {
        if ($parentCommentId === null) {
            return null;
        }

        $parent = Comment::find($parentCommentId);

        if (! $parent || $parent->task_id !== $task->id || $parent->parent_comment_id !== null) {
            throw ValidationException::withMessages(['parent_comment_id' => 'Replies can only be added to a top-level comment on this task.']);
        }

        return $parent->id;
    }

    /**
     * A reply notifies the ORIGINAL (top-level) comment's author,
     * unconditionally — see RepliedToCommentNotification's own docblock
     * for why this bypasses NotificationSetting entirely, the same way a
     * mention does. No-op for an ordinary top-level comment (no one to
     * notify) and for replying to your own comment (no self-notification,
     * matching the mention notification's own "never notify yourself"
     * rule). Independent of syncMentions() — a reply that also mentions
     * someone fires both notifications, to whichever recipients apply,
     * since they answer two different questions ("who authored the
     * comment being replied to" vs. "who was named in this text").
     */
    private function notifyReply(Comment $reply): void
    {
        if ($reply->parent_comment_id === null) {
            return;
        }

        $parent = Comment::with('user')->find($reply->parent_comment_id);

        if (! $parent || $parent->user_id === $reply->user_id) {
            return;
        }

        $parent->user->notify(new RepliedToCommentNotification($reply));
    }

    /**
     * Comment bodies arrive as editor HTML (or plain text from older
     * clients). The old 2000-character cap applied to what a person typed,
     * so it's now measured on the visible text — HTML markup mustn't eat
     * into it — with a separate generous cap on the raw HTML so a crafted
     * request can't store an unbounded blob. Blank content (including the
     * editor's empty "<p></p>") counts as missing.
     */
    private function cleanBody(string $raw): string
    {
        $body = RichText::normalize($raw);

        if ($body === null) {
            throw ValidationException::withMessages(['body' => 'The body field is required.']);
        }

        if (mb_strlen(RichText::plainText($body)) > self::MAX_BODY_TEXT_LENGTH || mb_strlen($body) > self::MAX_BODY_HTML_LENGTH) {
            throw ValidationException::withMessages(['body' => 'The body field must not be greater than '.self::MAX_BODY_TEXT_LENGTH.' characters.']);
        }

        return $body;
    }

    /**
     * Validates the submitted mention IDs against who's actually eligible
     * for this task — Task::viewableUsers(), the exact set of people who
     * could pass TaskPolicy::view() for this task, NOT the broader
     * project_staff/project_clients "assignable to this project" set
     * ValidatesTaskAssignment answers. Those two used to be treated as
     * the same thing here, which was a real permission leak: a staff
     * member attached to the project but lacking department access to
     * this specific task could be mentioned into (and notified about) a
     * task they can't actually open. Then syncs the pivot and notifies
     * only newly attached rows. sync()'s 'attached' list is what makes
     * "mention the same person twice" and "re-save an unchanged mention
     * on edit" both notify at most once — a dedupe already handled by the
     * pivot itself, not something this method needs to track separately.
     */
    private function syncMentions(Comment $comment, Task $task, array $mentionedIds): void
    {
        $viewableUserIds = $task->viewableUsers()->pluck('id')->all();

        $eligibleIds = collect($mentionedIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->filter(fn ($id) => $id !== $comment->user_id && in_array($id, $viewableUserIds, true))
            ->values()
            ->all();

        $result = $comment->mentionedUsers()->sync($eligibleIds);

        if (empty($result['attached'])) {
            return;
        }

        User::whereIn('id', $result['attached'])->get()->each(
            fn (User $user) => $user->notify(new MentionedInCommentNotification($task))
        );
    }
}
