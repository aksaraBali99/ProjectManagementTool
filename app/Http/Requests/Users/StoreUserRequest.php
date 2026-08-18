<?php

namespace App\Http\Requests\Users;

use App\Models\Organization;
use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:255', 'unique:users,employee_id'],
            'email' => ['required', 'email:rfc,filter', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', new ValidPhoneNumber],
            'roles' => ['required', 'array'],
            'roles.*' => ['required', 'in:none,staff,management'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
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
