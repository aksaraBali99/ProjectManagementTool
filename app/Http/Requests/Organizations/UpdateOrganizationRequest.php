<?php

namespace App\Http\Requests\Organizations;

use App\Http\Requests\Organizations\Concerns\ValidatesTaskCode;
use App\Models\Organization;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UpdateOrganizationRequest extends FormRequest
{
    use ValidatesTaskCode;

    public const TASK_CODE_LOCKED_MESSAGE = "The task code can't change once the company has tasks.";

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $organization = $this->organization();

        return [
            'name' => ['required', 'string', 'max:255'],
            // task #118 phase 2: editable (and required) until the company
            // has issued a task number. After that it is locked: it may be
            // left out, or sent unchanged, but never changed.
            'code' => $organization->hasIssuedTaskNumbers()
                ? ['nullable', 'string', function (string $attribute, mixed $value, Closure $fail) use ($organization) {
                    if ($value !== null && $value !== $organization->code) {
                        $fail(self::TASK_CODE_LOCKED_MESSAGE);
                    }
                }]
                : $this->taskCodeRules($organization),
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
            'completed_window_days.in' => 'The completed tasks window must be '.Arr::join(Organization::COMPLETED_WINDOW_OPTIONS, ', ', ' or ').' days.',
        ] + $this->taskCodeMessages($this->organization());
    }

    private function organization(): Organization
    {
        return $this->route('organization');
    }
}
