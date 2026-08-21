<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Users\Concerns\ValidatesCompanyRoles;
use App\Models\Role;
use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    use ValidatesCompanyRoles;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assignableSlugs = Role::assignableInCompany()->pluck('slug')->all();

        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:255', 'unique:users,employee_id'],
            'email' => ['required', 'email:rfc,filter', 'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', new ValidPhoneNumber],
            'roles' => ['required', 'array'],
            'roles.*' => ['required', Rule::in(array_merge(['none'], $assignableSlugs))],
            'grant_super_admin' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address.',
            'email.regex' => 'Please enter a valid email address.',
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_id' => 'Employee ID',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $roles = $this->input('roles', []);

            // Skipped if granting Super Admin — global access makes a
            // per-company role optional, same as an already-global user.
            $grantingSuperAdmin = $this->user()?->isOwner() && $this->boolean('grant_super_admin');

            $this->validateCompanyRoles($validator, $roles, $grantingSuperAdmin);
            $this->validateSuperAdminGrant($validator, $roles, $grantingSuperAdmin, targetAlreadySuperAdmin: false);
        });
    }
}
