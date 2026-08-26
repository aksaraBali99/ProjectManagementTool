<?php

namespace App\Http\Requests\Import;

use Illuminate\Foundation\Http\FormRequest;

class UploadImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx', 'max:'.config('import.max_upload_kb')],
        ];
    }
}
