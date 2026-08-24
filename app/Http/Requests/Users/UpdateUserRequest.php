<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\Users\Concerns\ValidatesCompanyRoles;
use App\Models\Role;
use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    use ValidatesCompanyRoles;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Owner itself stays fully locked (unmanageable via this form,
     * regardless of who's editing) — but a target who only holds Super
     * Admin, or no global role at all, gets the normal editable fields,
     * since an Owner viewer can now grant/revoke Super Admin here too.
     */
    private function targetIsOwner(): bool
    {
        return $this->route('user')->roles()->where('slug', Role::OWNER)->exists();
    }

    private function targetIsSuperAdmin(): bool
    {
        return $this->route('user')->roles()->where('slug', Role::SUPER_ADMIN)->exists();
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

        if (! $this->targetIsOwner()) {
            $assignableSlugs = Role::assignableInCompany()->pluck('slug')->all();

            $rules['roles'] = ['required', 'array'];
            $rules['roles.*'] = ['required', Rule::in(array_merge(['none'], $assignableSlugs))];
            $rules['grant_super_admin'] = ['sometimes', 'boolean'];
            $rules['access_permissions'] = ['sometimes', 'array'];
            $rules['access_permissions.*'] = ['integer', 'exists:departments,id'];
        }

        return $rules;
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
        if ($this->targetIsOwner()) {
            return;
        }

        $validator->after(function (Validator $validator) {
            $roles = $this->input('roles', []);
            $grantingSuperAdmin = $this->user()?->isOwner() && $this->boolean('grant_super_admin');

            $this->validateCompanyRoles($validator, $roles, $grantingSuperAdmin);
            $this->validateSuperAdminGrant($validator, $roles, $grantingSuperAdmin, $this->targetIsSuperAdmin());
        });
    }
}
