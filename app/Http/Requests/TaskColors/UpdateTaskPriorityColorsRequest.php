<?php

namespace App\Http\Requests\TaskColors;

use App\Enums\Priority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTaskPriorityColorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Same shape/reasoning as UpdateTaskStatusColorsRequest, but under its
     * own field name (priority_colors, not colors) — both sections live
     * on the same settings page and submit independently, so sharing a
     * field name would make a Status-form error also light up the
     * Priority section's @error('colors') block, and vice versa.
     */
    public function rules(): array
    {
        $priorities = Priority::values();

        return [
            'priority_colors' => ['required', 'array', 'size:'.count($priorities)],
            'priority_colors.*.background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'priority_colors.*.text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $submittedPriorities = array_keys($this->input('priority_colors', []));
            $missing = array_diff(Priority::values(), $submittedPriorities);

            if (! empty($missing)) {
                $validator->errors()->add('priority_colors', 'Every priority needs a color.');
            }

            $unknown = array_diff($submittedPriorities, Priority::values());

            if (! empty($unknown)) {
                $validator->errors()->add('priority_colors', 'One of the submitted priorities is not recognized.');
            }
        });
    }
}
