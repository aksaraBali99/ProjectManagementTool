<?php

namespace App\Http\Requests\Organizations;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'accent_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            // task #72 phase 7. Who may change it at all is the controller's
            // Gate::authorize('update') — owners and super admins only. Absent
            // means unchanged; the edit form always sends it.
            'completed_window_days' => ['sometimes', 'required', 'integer', Rule::in(Organization::COMPLETED_WINDOW_OPTIONS)],
        ];
    }

    public function messages(): array
    {
        return [
            'completed_window_days.in' => 'The completed tasks window must be 7, 14 or 30 days.',
        ];
    }
}
