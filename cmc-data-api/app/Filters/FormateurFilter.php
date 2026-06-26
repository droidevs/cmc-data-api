<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Formateur.
 *
 * Supported query params:
 *   q                  - free text search (nom_prenom, mle)
 *   mle                - exact
 *   nom_prenom         - partial match
 *   statut             - exact or comma list (OFPPT,Vacataire,Contractuel)
 *   pole_id            - exact or comma list (home pole)
 *   efp_mutualise      - exact pole id they actually teach in
 *   mutualise          - 1 | 0
 *   mhs_min / mhs_max  - monthly hour quota range
 *   has_affectations   - 1 | 0  (filters formateurs with/without any module assignment)
 *   email_edu          - partial match
 *   sort               - nom_prenom|mle|mhs|statut (prefix "-" for desc)
 */
class FormateurFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'q' => 'search',
            'mle' => 'filterMle',
            'nom_prenom' => 'filterNomPrenom',
            'statut' => 'filterStatut',
            'pole_id' => 'filterPoleId',
            'efp_mutualise' => 'filterEfpMutualise',
            'mutualise' => 'filterMutualise',
            'mhs_min' => 'filterMhsMin',
            'mhs_max' => 'filterMhsMax',
            'has_affectations' => 'filterHasAffectations',
            'email_edu' => 'filterEmailEdu',
        ];
    }

    protected function sortable(): array
    {
        return ['nom_prenom', 'mle', 'mhs', 'statut', 'created_at'];
    }

    protected function search(mixed $value): void
    {
        $term = (string) $value;
        $this->builder->where(function ($q) use ($term) {
            $q->where('nom_prenom', 'like', "%{$term}%")
                ->orWhere('mle', 'like', "%{$term}%");
        });
    }

    protected function filterMle(mixed $value): void
    {
        $this->exact('mle', $value);
    }

    protected function filterNomPrenom(mixed $value): void
    {
        $this->like('nom_prenom', $value);
    }

    protected function filterStatut(mixed $value): void
    {
        $this->inList('statut', $value);
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->inList('pole_id', $value);
    }

    protected function filterEfpMutualise(mixed $value): void
    {
        $this->inList('efp_mutualise', $value);
    }

    protected function filterMutualise(mixed $value): void
    {
        $this->boolean('mutualise', $value);
    }

    protected function filterMhsMin(mixed $value): void
    {
        $this->min('mhs', $value);
    }

    protected function filterMhsMax(mixed $value): void
    {
        $this->max('mhs', $value);
    }

    protected function filterHasAffectations(mixed $value): void
    {
        if ($this->toBool($value)) {
            $this->builder->has('affectations');
        } else {
            $this->builder->doesntHave('affectations');
        }
    }

    protected function filterEmailEdu(mixed $value): void
    {
        $this->like('email_edu', $value);
    }
}
