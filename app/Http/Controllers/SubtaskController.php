<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment;
use App\Models\Subtask;
use App\Models\Task;
use App\Support\DateRange;
use App\Support\TaskDueDateExtension;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class SubtaskController extends Controller
{
    use ValidatesTaskAssignment;

    public function store(Request $request, Task $task): JsonResponse
    {
        Gate::authorize('create', [Subtask::class, $task]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
        ]);

        if (! empty($data['assignee_id']) && ! $this->isAssignableStaffForProject($task->project, $task->department_id, $data['assignee_id'])) {
            abort(422, 'Assignee must be assigned to this project.');
        }

        $start = DateRange::normalize($data['start_date'] ?? null);
        $due = DateRange::normalize($data['due_date'] ?? null);

        // SOL-0022: the Add row starts out with the task's own start and
        // due dates. A task started late (moved to In Progress after its
        // due date, see TaskObserver::saving()) has start after due, so
        // checking those untouched defaults would refuse every new
        // subtask until the user changed a date they never chose.
        $inheritedDates = $start === DateRange::normalize($task->start_date)
            && $due === DateRange::normalize($task->due_date);

        if (! $inheritedDates && DateRange::dueBeforeStart($start, $due)) {
            throw ValidationException::withMessages(['due_date' => DateRange::DUE_BEFORE_START]);
        }

        $newTaskDue = TaskDueDateExtension::requiredFor($task, $due);
        if ($newTaskDue !== null && ! $request->boolean(TaskDueDateExtension::FLAG)) {
            return $this->taskDueExtensionNeeded($task, $newTaskDue);
        }

        $subtask = DB::transaction(function () use ($task, $data, $newTaskDue) {
            $subtask = $task->subtasks()->create($data);
            if ($newTaskDue !== null) {
                TaskDueDateExtension::extend($task);
            }

            return $subtask;
        });
        $subtask->setRelation('task', $task);

        return response()->json([
            'subtask' => $subtask,
            'task_due_date' => DateRange::normalize($task->due_date),
        ], 201);
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
            'description' => ['sometimes', 'nullable', 'string'],
            'assignee_id' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'start_date' => ['sometimes', 'nullable', 'date'],
        ]);

        if (! empty($data['assignee_id']) && ! $this->isAssignableStaffForProject($subtask->task->project, $subtask->task->department_id, $data['assignee_id'])) {
            abort(422, 'Assignee must be assigned to this project.');
        }

        // SOL-0022: the page saves one field per request, so the pair to
        // check is the date that was sent plus the stored one that wasn't.
        // array_key_exists, not isset: an explicit null clears a date. A
        // request without a date (title, assignee) is never checked, so
        // a subtask already holding an invalid pair stays editable.
        $sentStart = array_key_exists('start_date', $data);
        $sentDue = array_key_exists('due_date', $data);
        $storedDue = DateRange::normalize($subtask->due_date);
        $start = $sentStart ? DateRange::normalize($data['start_date']) : DateRange::normalize($subtask->start_date);
        $due = $sentDue ? DateRange::normalize($data['due_date']) : $storedDue;

        if (($sentStart || $sentDue) && DateRange::dueBeforeStart($start, $due)) {
            throw ValidationException::withMessages([$sentDue ? 'due_date' : 'start_date' => DateRange::DUE_BEFORE_START]);
        }

        $task = $subtask->task;
        $newTaskDue = $sentDue && $due !== $storedDue
            ? TaskDueDateExtension::requiredFor($task, $due, $subtask->id)
            : null;
        if ($newTaskDue !== null && ! $request->boolean(TaskDueDateExtension::FLAG)) {
            return $this->taskDueExtensionNeeded($task, $newTaskDue);
        }

        DB::transaction(function () use ($subtask, $task, $data, $newTaskDue) {
            $subtask->update($data);
            if ($newTaskDue !== null) {
                TaskDueDateExtension::extend($task);
            }
        });

        return response()->json([
            'subtask' => $subtask,
            'task_due_date' => DateRange::normalize($task->due_date),
        ]);
    }

    public function destroy(Subtask $subtask): JsonResponse
    {
        Gate::authorize('delete', $subtask);

        $subtask->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * SOL-0022: the subtask would be due after its task. Nothing is
     * saved; the page asks the user, then sends the same request again
     * with extend_task_due_date. Shaped like a validation error, so the
     * page's existing error handling shows the message either way.
     */
    private function taskDueExtensionNeeded(Task $task, string $newTaskDue): JsonResponse
    {
        $message = TaskDueDateExtension::subtaskMessage(DateRange::normalize($task->due_date), $newTaskDue);

        return response()->json([
            'message' => $message,
            'errors' => ['due_date' => [$message]],
            'needs_task_due_extension' => true,
            'new_task_due_date' => $newTaskDue,
        ], 422);
    }
}
