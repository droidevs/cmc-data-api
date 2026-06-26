<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Enums\AffectationMode;
use App\Rules\CsvOfEnum;
use App\Rules\CsvOfIntegers;
use App\Rules\CsvOfStrings;

class AffectationFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'groupe_id' => ['sometimes', new CsvOfIntegers],
            'module_code' => ['sometimes', new CsvOfStrings(maxItemLength: 50)],
            'formateur_mle' => ['sometimes', new CsvOfStrings(max: 50, maxItemLength: 20)],
            'mode' => ['sometimes', new CsvOfEnum(array_column(AffectationMode::cases(), 'value'))],
            'mh_affecte_min' => ['sometimes', 'numeric', 'min:0'],
            'mh_affecte_max' => ['sometimes', 'numeric', 'min:0', 'gte:mh_affecte_min'],
            'mh_affecte_syn_min' => ['sometimes', 'numeric', 'min:0'],
            'mh_affecte_syn_max' => ['sometimes', 'numeric', 'min:0', 'gte:mh_affecte_syn_min'],
            'mh_totale_min' => ['sometimes', 'numeric', 'min:0'],
            'mh_totale_max' => ['sometimes', 'numeric', 'min:0', 'gte:mh_totale_min'],
            'pole_id' => ['sometimes', 'integer', 'min:1'],
            'filiere_code' => ['sometimes', 'string', 'max:50'],
            'has_seances' => ['sometimes', 'boolean'],
        ];
    }

    protected function sortable(): array
    {
        return ['mh_affecte', 'mh_affecte_syn', 'mode', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return [
            'groupe',
            'module',
            'formateur',
            'formateurSyn',
            'seances',
            'seances.timeRange',
            'seances.espace',
            'groupe.annee',
            'groupe.annee.filiere',
        ];
    }
}
