<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Niveau.
 *
 * Supported query params:
 *   q / libelle - partial match
 *   sort        - libelle (prefix "-" for desc)
 */
class NiveauFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'q' => 'search',
            'libelle' => 'filterLibelle',
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'created_at'];
    }

    protected function search(mixed $value): void
    {
        $this->like('libelle', $value);
    }

    protected function filterLibelle(mixed $value): void
    {
        $this->like('libelle', $value);
    }
}
