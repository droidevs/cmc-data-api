<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Filiere.
 *
 * Supported query params:
 *   q                   - free text search (libelle, code_filiere)
 *   code_filiere         - exact or comma list
 *   libelle              - partial match
 *   pole_id              - exact or comma list
 *   niveau_id            - exact or comma list
 *   type_formation_id    - exact or comma list
 *   secteur              - exact or comma list
 *   sort                 - libelle|code_filiere (prefix "-" for desc)
 */
class FiliereFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'q' => 'search',
            'code_filiere' => 'filterCodeFiliere',
            'libelle' => 'filterLibelle',
            'pole_id' => 'filterPoleId',
            'niveau_id' => 'filterNiveauId',
            'type_formation_id' => 'filterTypeFormationId',
            'secteur' => 'filterSecteur',
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'code_filiere', 'created_at'];
    }

    protected function search(mixed $value): void
    {
        $term = (string) $value;
        $this->builder->where(function ($q) use ($term) {
            $q->where('libelle', 'like', "%{$term}%")
                ->orWhere('code_filiere', 'like', "%{$term}%");
        });
    }

    protected function filterCodeFiliere(mixed $value): void
    {
        $this->inList('code_filiere', $value);
    }

    protected function filterLibelle(mixed $value): void
    {
        $this->like('libelle', $value);
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->inList('pole_id', $value);
    }

    protected function filterNiveauId(mixed $value): void
    {
        $this->inList('niveau_id', $value);
    }

    protected function filterTypeFormationId(mixed $value): void
    {
        $this->inList('type_formation_id', $value);
    }

    protected function filterSecteur(mixed $value): void
    {
        $this->inList('secteur', $value);
    }
}
