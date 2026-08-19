<?php

namespace App\Http\Requests\Departments;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_ids' => ['required', 'array', 'min:1'],
            'organization_ids.*' => ['integer', 'exists:organizations,id'],
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    /**
     * Creating one department per selected company means the usual
     * Rule::unique (scoped to a single organization_id) doesn't fit —
     * each selected company needs its own name-uniqueness check instead.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $name = $this->input('name');
            $organizationIds = $this->input('organization_ids');

            if (! is_string($name) || $name === '' || ! is_array($organizationIds)) {
                return;
            }

            $conflictingOrgId = Department::where('name', $name)
                ->whereIn('organization_id', $organizationIds)
                ->value('organization_id');

            if ($conflictingOrgId !== null) {
                $orgName = Organization::find($conflictingOrgId)?->name ?? 'a selected company';
                $validator->errors()->add('name', "A department named \"{$name}\" already exists in {$orgName}.");
            }
        });
    }
}
