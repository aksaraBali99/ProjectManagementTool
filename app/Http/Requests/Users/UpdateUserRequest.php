<?php

namespace App\Http\Requests\Users;

use App\Models\Organization;
use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        $rules = [
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:255', Rule::unique('users', 'employee_id')->ignore($userId)],
            'email' => ['required', 'email:rfc,filter', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['required', 'string', 'max:30', new ValidPhoneNumber],
        ];

        if (! $this->route('user')->hasGlobalRole()) {
            $rules['roles'] = ['required', 'array'];
            $rules['roles.*'] = ['required', 'in:none,staff,management'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Please enter valid email address',
            'email.regex' => 'Please enter valid email address',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        if ($this->route('user')->hasGlobalRole()) {
            return;
        }

        $validator->after(function (Validator $validator) {
            $roles = $this->input('roles', []);

            $validOrgIds = Organization::query()->pluck('id')->map(fn ($id) => (string) $id)->all();
            foreach (array_keys($roles) as $organizationId) {
                if (! in_array((string) $organizationId, $validOrgIds, true)) {
                    $validator->errors()->add('roles', 'One of the selected companies is invalid.');

                    return;
                }
            }

            if (! collect($roles)->contains(fn ($role) => $role !== 'none')) {
                $validator->errors()->add('roles', 'Assign this user a role in at least one company.');
            }
        });
    }
}
