<?php

namespace App\Http\Requests\Departments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departmentId = $this->route('department')->id;

        return [
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')
                    ->where('organization_id', $this->input('organization_id'))
                    ->ignore($departmentId),
            ],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function attributes(): array
    {
        return [
            'organization_id' => 'company',
        ];
    }
}
