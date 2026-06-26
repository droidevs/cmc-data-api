<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Groupe.
 *
 * Supported query params:
 *   q                   - free text search (code)
 *   code                 - partial match
 *   annee_id             - exact or comma list
 *   mode                 - exact or comma list (Résidentiel, Alternance, ...)
 *   effectif_min/max     - group size range
 *   filiere_code         - via annee.filiere_code
 *   pole_id              - via annee.filiere.pole_id
 *   niveau_id            - via annee.filiere.niveau_id
 *   has_stagiaires       - 1 | 0
 *   sort                 - code|effectif (prefix "-" for desc)
 */
class GroupeFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'q' => 'search',
            'code' => 'filterCode',
            'annee_id' => 'filterAnneeId',
            'mode' => 'filterMode',
            'effectif_min' => 'filterEffectifMin',
            'effectif_max' => 'filterEffectifMax',
            'filiere_code' => 'filterFiliereCode',
            'pole_id' => 'filterPoleId',
            'niveau_id' => 'filterNiveauId',
            'has_stagiaires' => 'filterHasStagiaires',
        ];
    }

    protected function sortable(): array
    {
        return ['code', 'effectif', 'created_at'];
    }

    protected function search(mixed $value): void
    {
        $this->like('code', $value);
    }

    protected function filterCode(mixed $value): void
    {
        $this->like('code', $value);
    }

    protected function filterAnneeId(mixed $value): void
    {
        $this->inList('annee_id', $value);
    }

    protected function filterMode(mixed $value): void
    {
        $this->inList('mode', $value);
    }

    protected function filterEffectifMin(mixed $value): void
    {
        $this->min('effectif', $value);
    }

    protected function filterEffectifMax(mixed $value): void
    {
        $this->max('effectif', $value);
    }

    protected function filterFiliereCode(mixed $value): void
    {
        $this->builder->whereHas('annee', fn ($q) => $q->where('filiere_code', $value));
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->builder->whereHas('annee.filiere', fn ($q) => $q->where('pole_id', $value));
    }

    protected function filterNiveauId(mixed $value): void
    {
        $this->builder->whereHas('annee.filiere', fn ($q) => $q->where('niveau_id', $value));
    }

    protected function filterHasStagiaires(mixed $value): void
    {
        if ($this->toBool($value)) {
            $this->builder->has('stagiaires');
        } else {
            $this->builder->doesntHave('stagiaires');
        }
    }
}
