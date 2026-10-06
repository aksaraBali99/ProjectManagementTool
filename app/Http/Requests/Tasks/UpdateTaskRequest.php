<?php

namespace App\Http\Requests\Tasks;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Support\RichText;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    use ValidatesTaskAssignment;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Same normalization as StoreTaskRequest: sanitize editor HTML, and
        // treat a blank editor ("<p></p>") as no description. Only strings are
        // normalized; anything else (a malformed array) is left for the
        // 'string' rule to reject as a validation error rather than a TypeError.
        if (is_string($this->input('description'))) {
            $this->merge(['description' => RichText::normalize($this->input('description'))]);
        }
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:200000'],
            'priority' => ['required', Rule::enum(Priority::class)],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'due_date' => ['nullable', 'date'],
            'start_date' => ['nullable', 'date'],
        ];
    }

    public function attributes(): array
    {
        return [
            'project_id' => 'project',
            'department_id' => 'department',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $project = Project::find($this->input('project_id'));

            if (! $project) {
                return;
            }

            $departmentId = $this->input('department_id');
            if ($departmentId && ! Department::withoutGlobalScopes()->where('id', $departmentId)->where('organization_id', $project->organization_id)->exists()) {
                $validator->errors()->add('department_id', 'Select a department that belongs to the chosen project\'s company.');
            }

            $assigneeId = $this->input('assignee_id');
            if ($assigneeId && ! $this->assignmentIsUnchanged($assigneeId, $project->id, $departmentId)
                && ! $this->isAssignableStaffForProject($project, (int) $departmentId, $assigneeId)) {
                $validator->errors()->add('assignee_id', 'Select a user assigned to this project.');
            }
        });
    }

    /**
     * Is this submit leaving the assignment exactly as it already is —
     * same assignee, same project, same department?
     *
     * task #70 follow-up: the unified eligibility rule governs FUTURE
     * assignment, which is what that change's own description promised,
     * but the validation above enforced it on every save. An assignee
     * who was valid when assigned and later stopped qualifying (their
     * department access revoked, or the task moved) is still shown in
     * the Assignee dropdown — the Edit page injects them as the selected
     * option precisely so they aren't silently blanked. Re-validating
     * that untouched value on save meant the task could no longer be
     * saved AT ALL: editing only the description (the Description field
     * autosaves by submitting this whole form on blur) failed with
     * "Select a user assigned to this project.", naming a field the user
     * never touched and giving no way forward but reassigning the task.
     *
     * So an unchanged assignment is accepted as-is. Changing the
     * assignee re-validates, obviously — and so does changing the
     * project or department, since either forms a NEW (assignee,
     * project, department) combination that nobody has ever validated,
     * even though the assignee id itself didn't move. Edits to unrelated
     * fields (title, status, dates, description) don't re-run it.
     */
    private function assignmentIsUnchanged(mixed $assigneeId, int $projectId, mixed $departmentId): bool
    {
        $task = $this->route('task');

        if (! $task instanceof Task) {
            return false;
        }

        return (int) $assigneeId === $task->assignee_id
            && $projectId === $task->project_id
            && (int) $departmentId === $task->department_id;
    }
}
