<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionedInCommentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    use ValidatesTaskAssignment;

    public function index(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $comments = $task->comments()->with('user')->orderBy('created_at')->get();

        return response()->json([
            'comments' => $comments->map(fn (Comment $comment) => [
                'id' => $comment->id,
                'body' => $comment->body,
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
            'body' => ['required', 'string', 'max:2000'],
            'mentioned_user_ids' => ['sometimes', 'array'],
            'mentioned_user_ids.*' => ['integer'],
        ]);

        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        $this->syncMentions($comment, $task, $data['mentioned_user_ids'] ?? []);

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
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
            'body' => ['required', 'string', 'max:2000'],
            'mentioned_user_ids' => ['sometimes', 'array'],
            'mentioned_user_ids.*' => ['integer'],
        ]);

        $comment->update(['body' => $data['body']]);

        $this->syncMentions($comment, $comment->task, $data['mentioned_user_ids'] ?? []);

        return response()->json(['comment' => ['id' => $comment->id, 'body' => $comment->body]]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * Validates the submitted mention IDs against who's actually eligible
     * for this task (the same project_staff/project_clients set the
     * Assignee dropdown offers — ValidatesTaskAssignment's own definition
     * of "eligible"), then syncs the pivot and notifies only newly
     * attached rows. sync()'s 'attached' list is what makes "mention the
     * same person twice" and "re-save an unchanged mention on edit" both
     * notify at most once — a dedupe already handled by the pivot itself,
     * not something this method needs to track separately.
     */
    private function syncMentions(Comment $comment, Task $task, array $mentionedIds): void
    {
        $eligibleIds = collect($mentionedIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->filter(fn ($id) => $id !== $comment->user_id && $this->isAssignableStaffForProject($task->project, $id))
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
