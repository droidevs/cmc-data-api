<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

class TypeFormationFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'min:1', 'max:100'],
            'libelle' => ['sometimes', 'string', 'min:1', 'max:100'],
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return ['filieres'];
    }
}
