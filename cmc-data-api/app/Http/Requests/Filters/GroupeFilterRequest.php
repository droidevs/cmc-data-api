<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Rules\CsvOfIntegers;
use App\Rules\CsvOfStrings;

class GroupeFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'min:1', 'max:100'],
            'code' => ['sometimes', 'string', 'min:1', 'max:50'],
            'annee_id' => ['sometimes', new CsvOfIntegers],
            // Mode is free text in source data ("Résidentiel" | "Alternance" | ...),
            // no backing enum exists in the codebase — validate as a bounded CSV
            // of strings rather than inventing values that might not match imports.
            'mode' => ['sometimes', new CsvOfStrings(max: 10, maxItemLength: 50)],
            'effectif_min' => ['sometimes', 'integer', 'min:0'],
            'effectif_max' => ['sometimes', 'integer', 'min:0', 'gte:effectif_min'],
            'filiere_code' => ['sometimes', 'string', 'max:50'],
            'pole_id' => ['sometimes', 'integer', 'min:1'],
            'niveau_id' => ['sometimes', 'integer', 'min:1'],
            'has_stagiaires' => ['sometimes', 'boolean'],
        ];
    }

    protected function sortable(): array
    {
        return ['code', 'effectif', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return ['annee', 'stagiaires', 'affectations'];
    }
}
