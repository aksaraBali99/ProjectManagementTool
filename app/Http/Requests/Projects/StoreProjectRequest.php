<?php

namespace App\Http\Requests\Projects;

use App\Enums\Priority;
use App\Enums\ProjectStatus;
use App\Models\Role;
use App\Rules\ValidClientUser;
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
                Rule::exists('org_members', 'user_id')
                    ->where('organization_id', $this->input('organization_id'))
                    ->whereNot('role_id', Role::where('slug', Role::CLIENT)->value('id')),
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
