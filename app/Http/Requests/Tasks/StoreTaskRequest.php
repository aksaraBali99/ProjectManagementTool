<?php

namespace App\Http\Requests\Tasks;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment;
use App\Models\Department;
use App\Models\Project;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    use ValidatesTaskAssignment;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $subtasks = collect($this->input('subtasks', []))
            ->map(fn ($row) => is_array($row) ? $row : [])
            ->map(fn ($row) => [
                'title' => trim((string) ($row['title'] ?? '')),
                'assignee_id' => $row['assignee_id'] ?: null,
                'due_date' => $row['due_date'] ?: null,
            ])
            ->filter(fn ($row) => $row['title'] !== '')
            ->values()
            ->all();

        $this->merge(['subtasks' => $subtasks]);
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::enum(Priority::class)],
            'status' => ['required', Rule::enum(TaskStatus::class)],
            'due_date' => ['nullable', 'date'],
            'subtasks' => ['array'],
            'subtasks.*.title' => ['required', 'string', 'max:255'],
            'subtasks.*.assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'subtasks.*.due_date' => ['nullable', 'date'],
        ];
    }

    /**
     * The Department and Assignee dropdowns are JS-filtered to the selected
     * project's company, but that's client-side only — re-check server-side
     * that whatever came in still actually belongs to that company, the same
     * defense-in-depth already applied to Projects' staff.* selection.
     */
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
            if ($assigneeId && ! $this->isAssignableStaffForProject($project, $assigneeId)) {
                $validator->errors()->add('assignee_id', 'Select a staff member assigned to this project.');
            }

            foreach ($this->input('subtasks', []) as $index => $subtask) {
                $subtaskAssigneeId = $subtask['assignee_id'] ?? null;
                if ($subtaskAssigneeId && ! $this->isAssignableStaffForProject($project, $subtaskAssigneeId)) {
                    $validator->errors()->add("subtasks.{$index}.assignee_id", 'Subtask assignee must be assigned to this project.');
                }
            }
        });
    }
}
