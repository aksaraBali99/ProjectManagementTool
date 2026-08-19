<?php

namespace App\Http\Requests\Projects;

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
        ]);
    }

    public function rules(): array
    {
        return [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'client' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:open,closed'],
            'priority' => ['required', 'in:high,medium,low'],
            'staff' => ['array'],
            'staff.*' => [
                'integer',
                Rule::exists('org_members', 'user_id')->where('organization_id', $this->input('organization_id')),
            ],
        ];
    }
}
