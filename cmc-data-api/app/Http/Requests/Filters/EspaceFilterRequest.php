<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

use App\Rules\CsvOfIntegers;

/**
 * Validates query params consumed by App\Filters\EspaceFilter.
 *
 * Note: available_date requires available_time_range_id to actually take
 * effect in EspaceFilter::filterAvailableDate() (it's silently a no-op
 * otherwise) — we make that requirement explicit instead of letting the
 * client send a useless param and wonder why nothing changed.
 */
class EspaceFilterRequest extends IndexFilterRequest
{
    protected function filterRules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'min:1', 'max:100'],
            'libelle' => ['sometimes', 'string', 'min:1', 'max:100'],
            'pole_id' => ['sometimes', new CsvOfIntegers],
            'capacite_min' => ['sometimes', 'integer', 'min:0'],
            'capacite_max' => ['sometimes', 'integer', 'min:0', 'gte:capacite_min'],
            'effectif' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'available_date' => ['sometimes', 'date', 'required_with:available_time_range_id'],
            'available_time_range_id' => ['sometimes', 'integer', 'min:1', 'required_with:available_date'],
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'capacite', 'created_at'];
    }

    protected function allowedIncludes(): array
    {
        return ['pole', 'seances'];
    }
}
