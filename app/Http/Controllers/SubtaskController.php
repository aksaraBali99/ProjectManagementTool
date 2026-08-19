<?php

namespace App\Http\Controllers;

use App\Models\OrgMember;
use App\Models\Role;
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
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        if (! empty($data['assignee_id']) && ! $this->assigneeBelongsToOrganization($data['assignee_id'], $task->organization_id)) {
            abort(422, 'Assignee must belong to the task\'s company.');
        }

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
            'title' => ['sometimes', 'string', 'max:255'],
            'assignee_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ]);

        if (! empty($data['assignee_id']) && ! $this->assigneeBelongsToOrganization($data['assignee_id'], $subtask->task->organization_id)) {
            abort(422, 'Assignee must belong to the task\'s company.');
        }

        $subtask->update($data);

        return response()->json(['subtask' => $subtask]);
    }

    public function destroy(Subtask $subtask): JsonResponse
    {
        Gate::authorize('delete', $subtask);

        $subtask->delete();

        return response()->json(['deleted' => true]);
    }

    private function assigneeBelongsToOrganization(int $userId, int $organizationId): bool
    {
        return OrgMember::where('organization_id', $organizationId)
            ->where('user_id', $userId)
            ->whereHas('role', fn ($query) => $query->where('slug', Role::STAFF))
            ->exists();
    }
}
