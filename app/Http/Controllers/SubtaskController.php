<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SubtaskController extends Controller
{
    public function store(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('create', [Subtask::class, $task]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $subtask = $task->subtasks()->create($data);

        return response()->json(['subtask' => $subtask], 201);
    }

    public function toggle(Subtask $subtask): JsonResponse
    {
        Gate::authorize('toggle', $subtask);

        $subtask->update(['is_done' => ! $subtask->is_done]);

        return response()->json(['subtask' => $subtask]);
    }

    public function update(Request $request, Subtask $subtask): JsonResponse
    {
        Gate::authorize('update', $subtask);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $subtask->update($data);

        return response()->json(['subtask' => $subtask]);
    }

    public function destroy(Subtask $subtask): JsonResponse
    {
        Gate::authorize('delete', $subtask);

        $subtask->delete();

        return response()->json(['deleted' => true]);
    }
}
