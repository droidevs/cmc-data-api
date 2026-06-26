<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Module.
 *
 * Supported query params:
 *   q                - free text search (libelle, code_module)
 *   code_module       - exact or comma list
 *   libelle           - partial match
 *   annee_id          - exact or comma list
 *   regional          - 1 | 0
 *   filiere_code      - via annee.filiere_code
 *   pole_id           - via annee.filiere.pole_id
 *   has_affectations  - 1 | 0
 *   sort              - libelle|code_module (prefix "-" for desc)
 */
class ModuleFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'q' => 'search',
            'code_module' => 'filterCodeModule',
            'libelle' => 'filterLibelle',
            'annee_id' => 'filterAnneeId',
            'regional' => 'filterRegional',
            'filiere_code' => 'filterFiliereCode',
            'pole_id' => 'filterPoleId',
            'has_affectations' => 'filterHasAffectations',
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'code_module', 'created_at'];
    }

    protected function search(mixed $value): void
    {
        $term = (string) $value;
        $this->builder->where(function ($q) use ($term) {
            $q->where('libelle', 'like', "%{$term}%")
                ->orWhere('code_module', 'like', "%{$term}%");
        });
    }

    protected function filterCodeModule(mixed $value): void
    {
        $this->inList('code_module', $value);
    }

    protected function filterLibelle(mixed $value): void
    {
        $this->like('libelle', $value);
    }

    protected function filterAnneeId(mixed $value): void
    {
        $this->inList('annee_id', $value);
    }

    protected function filterRegional(mixed $value): void
    {
        $this->boolean('regional', $value);
    }

    protected function filterFiliereCode(mixed $value): void
    {
        $this->builder->whereHas('annee', fn ($q) => $q->where('filiere_code', $value));
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->builder->whereHas('annee.filiere', fn ($q) => $q->where('pole_id', $value));
    }

    protected function filterHasAffectations(mixed $value): void
    {
        if ($this->toBool($value)) {
            $this->builder->has('affectations');
        } else {
            $this->builder->doesntHave('affectations');
        }
    }
}
