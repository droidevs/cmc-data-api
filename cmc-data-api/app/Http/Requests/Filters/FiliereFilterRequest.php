<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Rules\CsvOfIntegers;
use App\Rules\CsvOfStrings;

class FiliereFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'min:1', 'max:100'],
            'code_filiere' => ['sometimes', new CsvOfStrings(maxItemLength: 50)],
            'libelle' => ['sometimes', 'string', 'min:1', 'max:100'],
            'pole_id' => ['sometimes', new CsvOfIntegers],
            'niveau_id' => ['sometimes', new CsvOfIntegers],
            'type_formation_id' => ['sometimes', new CsvOfIntegers],
            'secteur' => ['sometimes', new CsvOfStrings],
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'code_filiere', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return ['pole', 'niveau', 'typeFormation', 'annees'];
    }
}
