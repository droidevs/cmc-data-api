<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Rules\CsvOfStrings;

class AnneeFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'filiere_code' => ['sometimes', new CsvOfStrings(maxItemLength: 50)],
            'libelle' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'pole_id' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return ['filiere', 'groupes', 'modules'];
    }
}
