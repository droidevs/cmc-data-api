<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Affectation.
 *
 * Supported query params:
 *   groupe_id            - exact or comma list
 *   module_code          - exact or comma list
 *   formateur_mle        - matches either formateur_mle or formateur_mle_syn, exact or comma list
 *   mode                 - exact or comma list (presentiel, synchrone, async)
 *   mh_affecte_min/max   - presentiel hours range
 *   mh_affecte_syn_min/max - sync hours range
 *   mh_totale_min/max    - combined (presentiel + sync) hours range
 *   pole_id              - via groupe.annee.filiere.pole_id
 *   filiere_code         - via groupe.annee.filiere_code
 *   has_seances          - 1 | 0
 *   sort                 - mh_affecte|mode (prefix "-" for desc)
 */
class AffectationFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'groupe_id' => 'filterGroupeId',
            'module_code' => 'filterModuleCode',
            'formateur_mle' => 'filterFormateurMle',
            'mode' => 'filterMode',
            'mh_affecte_min' => 'filterMhAffecteMin',
            'mh_affecte_max' => 'filterMhAffecteMax',
            'mh_affecte_syn_min' => 'filterMhAffecteSynMin',
            'mh_affecte_syn_max' => 'filterMhAffecteSynMax',
            'mh_totale_min' => 'filterMhTotaleMin',
            'mh_totale_max' => 'filterMhTotaleMax',
            'pole_id' => 'filterPoleId',
            'filiere_code' => 'filterFiliereCode',
            'has_seances' => 'filterHasSeances',
        ];
    }

    protected function sortable(): array
    {
        return ['mh_affecte', 'mh_affecte_syn', 'mode', 'created_at'];
    }

    protected function filterGroupeId(mixed $value): void
    {
        $this->inList('groupe_id', $value);
    }

    protected function filterModuleCode(mixed $value): void
    {
        $this->inList('module_code', $value);
    }

    protected function filterFormateurMle(mixed $value): void
    {
        $values = is_array($value) ? $value : array_filter(array_map('trim', explode(',', (string) $value)));
        $this->builder->where(function ($q) use ($values) {
            $q->whereIn('formateur_mle', $values)
                ->orWhereIn('formateur_mle_syn', $values);
        });
    }

    protected function filterMode(mixed $value): void
    {
        $this->inList('mode', $value);
    }

    protected function filterMhAffecteMin(mixed $value): void
    {
        $this->min('mh_affecte', $value);
    }

    protected function filterMhAffecteMax(mixed $value): void
    {
        $this->max('mh_affecte', $value);
    }

    protected function filterMhAffecteSynMin(mixed $value): void
    {
        $this->min('mh_affecte_syn', $value);
    }

    protected function filterMhAffecteSynMax(mixed $value): void
    {
        $this->max('mh_affecte_syn', $value);
    }

    protected function filterMhTotaleMin(mixed $value): void
    {
        $this->builder->whereRaw('(COALESCE(mh_affecte, 0) + COALESCE(mh_affecte_syn, 0)) >= ?', [$value]);
    }

    protected function filterMhTotaleMax(mixed $value): void
    {
        $this->builder->whereRaw('(COALESCE(mh_affecte, 0) + COALESCE(mh_affecte_syn, 0)) <= ?', [$value]);
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->builder->whereHas('groupe.annee.filiere', fn ($q) => $q->where('pole_id', $value));
    }

    protected function filterFiliereCode(mixed $value): void
    {
        $this->builder->whereHas('groupe.annee', fn ($q) => $q->where('filiere_code', $value));
    }

    protected function filterHasSeances(mixed $value): void
    {
        if ($this->toBool($value)) {
            $this->builder->has('seances');
        } else {
            $this->builder->doesntHave('seances');
        }
    }
}
