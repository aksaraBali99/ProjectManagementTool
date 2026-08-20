<?php

namespace App\Http\Requests\Projects;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'staff' => array_values(array_filter($this->input('staff', []), fn ($id) => $id !== null && $id !== '')),
        ]);
    }

    public function rules(): array
    {
        $organizationId = $this->route('project')->organization_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'client' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'priority' => ['required', Rule::enum(Priority::class)],
            'staff' => ['array'],
            'staff.*' => [
                'integer',
                Rule::exists('org_members', 'user_id')->where('organization_id', $organizationId),
            ],
        ];
    }
}
