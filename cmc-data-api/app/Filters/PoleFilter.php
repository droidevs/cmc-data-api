<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Pole.
 *
 * Supported query params:
 *   q / libelle - partial match on libelle
 *   has_formateurs / has_filieres / has_espaces - 1 | 0
 *   sort - libelle (prefix "-" for desc)
 */
class PoleFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'q' => 'search',
            'libelle' => 'filterLibelle',
            'has_formateurs' => 'filterHasFormateurs',
            'has_filieres' => 'filterHasFilieres',
            'has_espaces' => 'filterHasEspaces',
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

    protected function filterHasFormateurs(mixed $value): void
    {
        $this->toBool($value) ? $this->builder->has('formateurs') : $this->builder->doesntHave('formateurs');
    }

    protected function filterHasFilieres(mixed $value): void
    {
        $this->toBool($value) ? $this->builder->has('filieres') : $this->builder->doesntHave('filieres');
    }

    protected function filterHasEspaces(mixed $value): void
    {
        $this->toBool($value) ? $this->builder->has('espaces') : $this->builder->doesntHave('espaces');
    }
}
