<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Rules\CsvOfIntegers;
use App\Rules\CsvOfStrings;

class ModuleFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'min:1', 'max:100'],
            'code_module' => ['sometimes', new CsvOfStrings(maxItemLength: 50)],
            'libelle' => ['sometimes', 'string', 'min:1', 'max:100'],
            'annee_id' => ['sometimes', new CsvOfIntegers],
            'regional' => ['sometimes', 'boolean'],
            'filiere_code' => ['sometimes', 'string', 'max:50'],
            'pole_id' => ['sometimes', 'integer', 'min:1'],
            'has_affectations' => ['sometimes', 'boolean'],
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'code_module', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return [
            'annee',
            'annee.filiere',
            'affectations',
            'affectations.groupe',
            'affectations.formateur',
        ];
    }
}
