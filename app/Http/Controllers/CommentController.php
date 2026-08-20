<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function index(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        $comments = $task->comments()->with('user')->orderBy('created_at')->get();

        return response()->json([
            'comments' => $comments->map(fn (Comment $comment) => [
                'id' => $comment->id,
                'body' => $comment->body,
                'user_name' => $comment->user->name,
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
        ]);

        $comment = $task->comments()->create([
            'user_id' => auth()->id(),
            'body' => $data['body'],
        ]);

        return response()->json([
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'user_name' => auth()->user()->name,
                'created_at' => $comment->created_at->format('M j, Y g:ia'),
            ],
        ], 201);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        Gate::authorize('update', $comment);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $comment->update($data);

        return response()->json(['comment' => ['id' => $comment->id, 'body' => $comment->body]]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return response()->json(['deleted' => true]);
    }
}
