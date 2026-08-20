<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment;
use App\Models\AuditLog;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SubtaskController extends Controller
{
    use ValidatesTaskAssignment;

    public function store(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('create', [Subtask::class, $task]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        if (! empty($data['assignee_id']) && ! $this->isAssignableStaffForProject($task->project, $data['assignee_id'])) {
            abort(422, 'Assignee must be assigned to this project.');
        }

        $subtask = $task->subtasks()->create($data);
        $subtask->setRelation('task', $task);

        $this->logAudit($subtask, 'created', collect($subtask->getAttributes())->only(['title', 'assignee_id', 'due_date'])->all());

        return response()->json(['subtask' => $subtask], 201);
    }

    public function toggle(Subtask $subtask): JsonResponse
    {
        Gate::authorize('toggle', $subtask);

        $subtask->update(['is_done' => ! $subtask->is_done]);

        $this->logAudit($subtask, $subtask->is_done ? 'completed' : 'reopened', ['is_done' => $subtask->is_done]);

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

        if (! empty($data['assignee_id']) && ! $this->isAssignableStaffForProject($subtask->task->project, $data['assignee_id'])) {
            abort(422, 'Assignee must be assigned to this project.');
        }

        $subtask->update($data);

        $changes = $subtask->getChanges();
        unset($changes['updated_at']);
        if (! empty($changes)) {
            $this->logAudit($subtask, 'updated', $changes);
        }

        return response()->json(['subtask' => $subtask]);
    }

    public function destroy(Subtask $subtask): JsonResponse
    {
        Gate::authorize('delete', $subtask);

        $changes = collect($subtask->getAttributes())->only(['title', 'assignee_id', 'due_date', 'is_done'])->all();

        $subtask->delete();

        $this->logAudit($subtask, 'deleted', $changes);

        return response()->json(['deleted' => true]);
    }

    private function logAudit(Subtask $subtask, string $action, array $changes = []): void
    {
        AuditLog::create([
            'organization_id' => $subtask->task->organization_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => Subtask::class,
            'entity_id' => $subtask->id,
            'changes' => $changes,
        ]);
    }
}
