<?php

namespace App\Http\Requests\Users;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;

        $rules = [
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
            'name' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:255', Rule::unique('users', 'employee_id')->ignore($userId)],
            'emails' => ['required', 'array', 'min:1'],
            'emails.*.label' => ['nullable', 'string', 'max:255'],
            'emails.*.value' => [
                'required', 'string', 'email:rfc,filter', 'max:255', 'distinct:ignore_case',
                Rule::unique('user_emails', 'email')->where(fn ($query) => $query->where('user_id', '!=', $userId)),
            ],
            'phones' => ['required', 'array', 'min:1'],
            'phones.*.label' => ['nullable', 'string', 'max:255'],
            'phones.*.value' => ['required', 'string', 'max:30', 'distinct', 'phone:ID,international'],
        ];

        if (! $this->route('user')->hasGlobalRole()) {
            $rules['roles'] = ['required', 'array'];
            $rules['roles.*'] = ['required', 'in:none,staff,management'];
        }

        return $rules;
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

    /**
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
