<?php

namespace App\Http\Requests\Tasks;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Http\Requests\Tasks\Concerns\ValidatesTaskAssignment;
use App\Models\Department;
use App\Models\Project;
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
            if ($assigneeId && ! $this->isAssignableStaffForProject($project, $assigneeId)) {
                $validator->errors()->add('assignee_id', 'Select a user assigned to this project.');
            }
        });
    }
}
