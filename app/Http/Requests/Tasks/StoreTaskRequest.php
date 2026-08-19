<?php

namespace App\Http\Requests\Tasks;

use App\Models\Department;
use App\Models\OrgMember;
use App\Models\Project;
use App\Models\Role;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'subtasks' => array_values(array_filter(
                array_map('trim', $this->input('subtasks', [])),
                fn ($title) => $title !== ''
            )),
        ]);
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:high,medium,low'],
            'status' => ['required', 'in:pending,in_progress,in_review,completed'],
            'due_date' => ['nullable', 'date'],
            'subtasks' => ['array'],
            'subtasks.*' => ['string', 'max:255'],
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
            if ($assigneeId && ! OrgMember::where('organization_id', $project->organization_id)
                ->where('user_id', $assigneeId)
                ->whereHas('role', fn ($query) => $query->where('slug', Role::STAFF))
                ->exists()) {
                $validator->errors()->add('assignee_id', 'Select a staff member who belongs to the chosen project\'s company.');
            }
        });
    }
}
