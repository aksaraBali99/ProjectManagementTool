<?php

namespace App\Http\Requests\TaskColors;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateTaskStatusColorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * One background_color/text_color pair required per TaskStatus case —
     * a hex string (3 or 6 digits after '#', matching what
     * <input type="color"> always submits) rather than a looser color
     * format, since that's the only kind of value this page's UI can ever
     * produce.
     */
    public function rules(): array
    {
        $statuses = TaskStatus::values();

        return [
            'colors' => ['required', 'array', 'size:'.count($statuses)],
            'colors.*.background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors.*.text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $submittedStatuses = array_keys($this->input('colors', []));
            $missing = array_diff(TaskStatus::values(), $submittedStatuses);

            if (! empty($missing)) {
                $validator->errors()->add('colors', 'Every status needs a color.');
            }

            $unknown = array_diff($submittedStatuses, TaskStatus::values());

            if (! empty($unknown)) {
                $validator->errors()->add('colors', 'One of the submitted statuses is not recognized.');
            }
        });
    }
}
