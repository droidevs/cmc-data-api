<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Enums\FormateurStatut;
use App\Rules\CsvOfEnum;
use App\Rules\CsvOfIntegers;

class FormateurFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'min:1', 'max:100'],
            'mle' => ['sometimes', 'string', 'max:20'],
            'nom_prenom' => ['sometimes', 'string', 'min:1', 'max:100'],
            'statut' => ['sometimes', new CsvOfEnum(array_column(FormateurStatut::cases(), 'value'), max: 3)],
            'pole_id' => ['sometimes', new CsvOfIntegers],
            'efp_mutualise' => ['sometimes', new CsvOfIntegers],
            'mutualise' => ['sometimes', 'boolean'],
            'mhs_min' => ['sometimes', 'numeric', 'min:0'],
            'mhs_max' => ['sometimes', 'numeric', 'min:0', 'gte:mhs_min'],
            'has_affectations' => ['sometimes', 'boolean'],
            'email_edu' => ['sometimes', 'string', 'min:1', 'max:150'],
        ];
    }

    protected function sortable(): array
    {
        return ['nom_prenom', 'mle', 'mhs', 'statut', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return ['pole', 'affectations', 'affectations.module', 'affectations.groupe'];
    }
}
