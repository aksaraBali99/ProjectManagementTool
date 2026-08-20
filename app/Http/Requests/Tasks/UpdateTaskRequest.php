<?php

namespace App\Http\Requests\Tasks;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Department;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            if ($assigneeId && ! OrgMember::where('organization_id', $project->organization_id)
                ->where('user_id', $assigneeId)
                ->whereHas('role', fn ($query) => $query->where('slug', Role::STAFF))
                ->exists()) {
                $validator->errors()->add('assignee_id', 'Select a staff member who belongs to the chosen project\'s company.');
            }
        });
    }
}
