<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Annee.
 *
 * Supported query params:
 *   filiere_code  - exact or comma list
 *   libelle       - exact (1 or 2)
 *   pole_id       - via filiere.pole_id
 *   sort          - libelle (prefix "-" for desc)
 */
class AnneeFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'filiere_code' => 'filterFiliereCode',
            'libelle' => 'filterLibelle',
            'pole_id' => 'filterPoleId',
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'created_at'];
    }

    protected function filterFiliereCode(mixed $value): void
    {
        $this->inList('filiere_code', $value);
    }

    protected function filterLibelle(mixed $value): void
    {
        $this->exact('libelle', $value);
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->builder->whereHas('filiere', fn ($q) => $q->where('pole_id', $value));
    }
}
