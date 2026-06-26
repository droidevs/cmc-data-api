<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Rules\CsvOfEnum;
use App\Rules\CsvOfIntegers;
use App\Rules\CsvOfStrings;

class SeanceFilterRequest extends IndexFilterRequest
{
    /** Seance::type isn't a backed enum in the codebase — values per SeanceResource docblock. */
    private const TYPES = ['cours', 'cc', 'efm', 'exam'];

    protected function filterRules(): array
    {
        return [
            'type' => ['sometimes', new CsvOfEnum(self::TYPES)],
            'date' => ['sometimes', 'date'],
            'date_from' => ['sometimes', 'date', 'before_or_equal:date_to'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
            'espace_id' => ['sometimes', new CsvOfIntegers],
            'time_range_id' => ['sometimes', new CsvOfIntegers],
            'affectation_id' => ['sometimes', new CsvOfIntegers],
            'groupe_id' => ['sometimes', 'integer', 'min:1'],
            'module_code' => ['sometimes', 'string', 'max:50'],
            'formateur_mle' => ['sometimes', new CsvOfStrings(max: 50, maxItemLength: 20)],
            'pole_id' => ['sometimes', 'integer', 'min:1'],
            'has_notes' => ['sometimes', 'boolean'],
            // 0 (Sunday) - 6 (Saturday); comma list, raw whereRaw with bound placeholders.
            'weekday' => ['sometimes', new CsvOfEnum(['0', '1', '2', '3', '4', '5', '6'])],
        ];
    }

    protected function sortable(): array
    {
        return ['date', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return [
            'affectation',
            'affectation.groupe',
            'affectation.module',
            'affectation.formateur',
            'timeRange',
            'espace',
            'espace.pole',
            'notes',
            'notes.stagiaire',
        ];
    }
}
