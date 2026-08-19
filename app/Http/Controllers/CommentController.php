<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
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
}
