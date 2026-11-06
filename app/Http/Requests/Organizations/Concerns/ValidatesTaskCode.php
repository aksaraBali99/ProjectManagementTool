<?php

namespace App\Http\Requests\Organizations\Concerns;

use App\Models\Organization;
use Illuminate\Validation\Rule;

/**
 * task #118 phase 2: the company's task code (BGS, as in BGS-0012) on
 * the Add/Edit Company forms. Shared by both requests so the format,
 * the "already used" check and their messages can't drift apart.
 */
trait ValidatesTaskCode
{
    public const TASK_CODE_FORMAT_MESSAGE = 'The task code must be exactly 3 letters (A to Z).';

    /**
     * " bva " is saved as BVA; an empty field counts as not sent. Only
     * text is tidied: anything else (a crafted code[]=ABC) is left for
     * the 'string' rule to reject, rather than failing here.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('code'))) {
            $code = strtoupper(trim($this->input('code')));
            $this->merge(['code' => $code === '' ? null : $code]);
        }
    }

    /**
     * The rules for a code the user may set: required, three letters,
     * not used by any other company (deactivated ones included).
     *
     * @return list<mixed>
     */
    protected function taskCodeRules(?Organization $ignore = null): array
    {
        return [
            'required',
            'string',
            'regex:/^[A-Z]{3}$/',
            Rule::unique('organizations', 'code')->ignore($ignore?->id),
        ];
    }

    /** @return array<string, string> */
    protected function taskCodeMessages(?Organization $ignore = null): array
    {
        return [
            'code.required' => 'The task code is required.',
            'code.regex' => self::TASK_CODE_FORMAT_MESSAGE,
            'code.unique' => self::taskCodeTakenMessage(is_string($this->input('code')) ? $this->input('code') : '', $ignore)
                ?? 'This task code is already used by another company.',
        ];
    }

    public function attributes(): array
    {
        return ['code' => 'task code'];
    }

    /**
     * "BVA is already used by Bali Virtual Academy.", or null when no
     * other company holds $code. The one lookup of who holds a code:
     * the unique rule's message, and the controller when the unique
     * index catches two companies saved at the same moment.
     */
    public static function taskCodeTakenMessage(string $code, ?Organization $ignore = null): ?string
    {
        $owner = Organization::where('code', $code)
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))
            ->value('name');

        return $owner === null ? null : "{$code} is already used by {$owner}.";
    }
}
