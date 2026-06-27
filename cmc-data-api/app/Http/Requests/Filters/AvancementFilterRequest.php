<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Rules\CsvOfIntegers;

class AvancementFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'groupe_id' => ['sometimes', new CsvOfIntegers],
            'module_id' => ['sometimes', new CsvOfIntegers],
            'filiere_code' => ['sometimes', 'string', 'max:50'],
            'termine' => ['sometimes', 'boolean'],
        ];
    }

    protected function sortable(): array
    {
        return ['groupe_id', 'module_id', 'mh_realisee_globale', 'taux_realisation_globale', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return ['groupe', 'groupe.annee', 'groupe.annee.filiere', 'module', 'module.annee', 'module.annee.filiere'];
    }
}
