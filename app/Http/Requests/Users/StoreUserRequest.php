<?php

namespace App\Http\Requests\Users;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'emails' => $this->dropBlankRows($this->input('emails', [])),
            'phones' => $this->dropBlankRows($this->input('phones', [])),
        ]);
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()],
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:255', 'unique:users,employee_id'],
            'emails' => ['required', 'array', 'min:1'],
            'emails.*.label' => ['nullable', 'string', 'max:255'],
            'emails.*.value' => ['required', 'string', 'email:rfc,filter', 'max:255', 'distinct:ignore_case', Rule::unique('user_emails', 'email')],
            'phones' => ['required', 'array', 'min:1'],
            'phones.*.label' => ['nullable', 'string', 'max:255'],
            'phones.*.value' => ['required', 'string', 'max:30', 'distinct', 'phone:ID,international'],
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

    /**
     * Drops rows the admin added but never filled in (e.g. clicked "+ Add"
     * then changed their mind) so they don't trip "required" validation.
     *
     * @param  array<int, array{label?: string, value?: string}>  $rows
     * @return array<int, array{label?: string, value?: string}>
     */
    private function dropBlankRows(array $rows): array
    {
        return array_values(array_filter(
            $rows,
            fn ($row) => trim((string) ($row['value'] ?? '')) !== ''
        ));
    }
}
