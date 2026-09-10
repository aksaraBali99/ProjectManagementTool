<?php

namespace App\Http\Requests\Projects;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Rules\ValidClientUser;
use App\Rules\ValidProjectStaffUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'staff' => array_values(array_filter($this->input('staff', []), fn ($id) => $id !== null && $id !== '')),
            'client' => $this->input('client') !== '' ? $this->input('client') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'client' => ['nullable', 'integer', new ValidClientUser],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'priority' => ['required', Rule::enum(Priority::class)],
            'staff' => ['array'],
            'staff.*' => [
                'integer',
                new ValidProjectStaffUser((int) $this->input('organization_id')),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'organization_id' => 'company',
        ];
    }
}
