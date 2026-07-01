<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Rules\CsvOfEnum;
use App\Rules\CsvOfIntegers;
use App\Rules\CsvOfStrings;

class NoteFilterRequest extends IndexFilterRequest
{
    /** Per NoteResource docblock: cc | efm | tp | th | syn | exam. */
    private const TYPES = ['cc', 'efm'];

    /** Per NoteResource docblock DECISION values. */
    private const DECISIONS = ['Admis', 'Redoublant', 'Abandon', 'Rattrapage'];

    protected function filterRules(): array
    {
        return [
            'seance_id' => ['sometimes', new CsvOfIntegers],
            'stagiaire_cef' => ['sometimes', new CsvOfStrings(max: 100, maxItemLength: 50)],
            'type' => ['sometimes', new CsvOfEnum(self::TYPES)],
            'decision' => ['sometimes', new CsvOfEnum(self::DECISIONS, max: 4)],
            'valeur_min' => ['sometimes', 'numeric', 'min:0', 'max:20'],
            'valeur_max' => ['sometimes', 'numeric', 'min:0', 'max:20', 'gte:valeur_min'],
            'passing' => ['sometimes', 'boolean'],
            'missing' => ['sometimes', 'boolean'],
            'groupe_id' => ['sometimes', 'integer', 'min:1'],
        ];
    }

    protected function sortable(): array
    {
        return ['valeur', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return [
            'seance',
            'seance.affectation',
            'seance.affectation.module',
            'seance.affectation.groupe',
            'seance.timeRange',
            'stagiaire',
            'stagiaire.groupe',
        ];
    }
}
