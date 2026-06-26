<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Enums\Genre;
use App\Rules\CsvOfEnum;
use App\Rules\CsvOfIntegers;

/**
 * Validates query params consumed by App\Filters\StagiaireFilter.
 *
 * Mirrors StagiaireFilter::filters() / sortable() exactly. See that class
 * for the authoritative param-to-behaviour mapping; this class is the
 * "front door" that rejects bad input before it reaches the query builder.
 */
class StagiaireFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'min:1', 'max:100'],
            'nom' => ['sometimes', 'string', 'min:1', 'max:100'],
            'prenom' => ['sometimes', 'string', 'min:1', 'max:100'],
            'cef' => ['sometimes', 'string', 'max:50'],
            'cni' => ['sometimes', 'string', 'max:50'],
            'genre' => ['sometimes', new CsvOfEnum(array_column(Genre::cases(), 'value'), max: 2)],
            'actif' => ['sometimes', 'boolean'],
            'groupe_id' => ['sometimes', new CsvOfIntegers],
            'groupe_code' => ['sometimes', 'string', 'min:1', 'max:50'],
            'annee_id' => ['sometimes', 'integer', 'min:1'],
            'filiere_code' => ['sometimes', 'string', 'max:50'],
            'pole_id' => ['sometimes', 'integer', 'min:1'],
            'niveau_id' => ['sometimes', 'integer', 'min:1'],
            'age_min' => ['sometimes', 'integer', 'min:0', 'max:120'],
            'age_max' => ['sometimes', 'integer', 'min:0', 'max:120', 'gte:age_min'],
            'date_naissance_from' => ['sometimes', 'date', 'before_or_equal:date_naissance_to'],
            'date_naissance_to' => ['sometimes', 'date', 'after_or_equal:date_naissance_from'],
        ];
    }

    protected function sortable(): array
    {
        return ['cef', 'nom', 'prenom', 'date_naissance', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return [
            'groupe',
            'groupe.annee',
            'groupe.annee.filiere',
            'notes',
            'notes.seance',
            'notes.seance.affectation',
            'notes.seance.affectation.module',
        ];
    }
}
