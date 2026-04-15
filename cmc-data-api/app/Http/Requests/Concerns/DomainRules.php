<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

trait DomainRules
{
    /** @return array<int, mixed> */
    protected function modeRules(bool $required = true): array
    {
        return array_values(array_filter([
            $required ? 'required' : 'sometimes',
            'string',
            'max:32',
            Rule::in(['presentiel', 'synchrone', 'async']),
        ]));
    }

    /** @return array<int, mixed> */
    protected function seanceTypeRules(bool $required = true): array
    {
        return array_values(array_filter([
            $required ? 'required' : 'sometimes',
            'string',
            'max:32',
            Rule::in(['cours', 'cc', 'efm', 'exam']),
        ]));
    }

    /** @return array<int, mixed> */
    protected function noteTypeRules(bool $required = true): array
    {
        return array_values(array_filter([
            $required ? 'required' : 'sometimes',
            'string',
            'max:32',
            Rule::in(['cc', 'efm', 'exam']),
        ]));
    }
}

