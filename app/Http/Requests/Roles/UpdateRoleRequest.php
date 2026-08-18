<?php

namespace App\Http\Requests\Roles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $role = $this->route('role');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
        ];

        if (! $role->is_system) {
            $rules['slug'] = ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('roles', 'slug')->ignore($role->id)];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $role = $this->route('role');

        if ($role->is_system && $this->has('slug') && $this->input('slug') !== $role->slug) {
            $validator->after(function (Validator $validator) {
                $validator->errors()->add('slug', 'The slug of a system role cannot be changed.');
            });
        }
    }
}
