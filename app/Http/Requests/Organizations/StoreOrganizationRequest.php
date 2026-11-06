<?php

namespace App\Http\Requests\Organizations;

use App\Http\Requests\Organizations\Concerns\ValidatesTaskCode;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    use ValidatesTaskCode;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            // task #118 phase 2: required on the form. A company created
            // without one elsewhere (the Excel import) still gets
            // Organization::creating()'s suggestion.
            'code' => $this->taskCodeRules(),
            'accent_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function messages(): array
    {
        return $this->taskCodeMessages();
    }
}
