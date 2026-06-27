<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Seance.
 *
 * Supported query params:
 *   type               - exact or comma list (cours, cc, efm, exam)
 *   date               - exact date
 *   date_from/date_to  - range
 *   espace_id          - exact or comma list
 *   time_range_id      - exact or comma list
 *   affectation_id     - exact or comma list
 *   groupe_id          - via affectation.groupe_id
 *   module_code        - via affectation.module_code
 *   formateur_mle      - via affectation.formateur_mle or formateur_mle_syn
 *   pole_id            - via affectation.groupe.annee.filiere.pole_id
 *   has_notes          - 1 | 0
 *   weekday            - 0 (Sunday) - 6 (Saturday), or comma list
 *   sort               - date|created_at (prefix "-" for desc)
 */
class SeanceFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'type' => 'filterType',
            'date' => 'filterDate',
            'date_from' => 'filterDateFrom',
            'date_to' => 'filterDateTo',
            'espace_id' => 'filterEspaceId',
            'time_range_id' => 'filterTimeRangeId',
            'affectation_id' => 'filterAffectationId',
            'groupe_id' => 'filterGroupeId',
            'module_code' => 'filterModuleCode',
            'formateur_mle' => 'filterFormateurMle',
            'pole_id' => 'filterPoleId',
            'has_notes' => 'filterHasNotes',
            'weekday' => 'filterWeekday',
        ];
    }

    protected function sortable(): array
    {
        return ['date', 'created_at'];
    }

    protected function filterType(mixed $value): void
    {
        $this->inList('type', $value);
    }

    protected function filterDate(mixed $value): void
    {
        $this->builder->whereDate('date', $value);
    }

    protected function filterDateFrom(mixed $value): void
    {
        $this->dateFrom('date', $value);
    }

    protected function filterDateTo(mixed $value): void
    {
        $this->dateTo('date', $value);
    }

    protected function filterEspaceId(mixed $value): void
    {
        $this->inList('espace_id', $value);
    }

    protected function filterTimeRangeId(mixed $value): void
    {
        $this->inList('time_range_id', $value);
    }

    protected function filterAffectationId(mixed $value): void
    {
        $this->inList('affectation_id', $value);
    }

    protected function filterGroupeId(mixed $value): void
    {
        $this->builder->whereHas('affectation', fn ($q) => $q->where('groupe_id', $value));
    }

    protected function filterModuleCode(mixed $value): void
    {
        $values = is_array($value) ? $value : array_filter(array_map('trim', explode(',', (string) $value)));
        $this->builder->whereHas('affectation.module', fn ($q) => $q->whereIn('code_module', $values));
    }

    protected function filterFormateurMle(mixed $value): void
    {
        $values = is_array($value) ? $value : array_filter(array_map('trim', explode(',', (string) $value)));
        $this->builder->whereHas('affectation', function ($q) use ($values) {
            $q->whereIn('formateur_mle', $values)
                ->orWhereIn('formateur_mle_syn', $values);
        });
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->builder->whereHas('affectation.groupe.annee.filiere', fn ($q) => $q->where('pole_id', $value));
    }

    protected function filterHasNotes(mixed $value): void
    {
        if ($this->toBool($value)) {
            $this->builder->has('notes');
        } else {
            $this->builder->doesntHave('notes');
        }
    }

    protected function filterWeekday(mixed $value): void
    {
        $days = is_array($value) ? $value : array_filter(array_map('trim', explode(',', (string) $value)));
        $days = array_values(array_map('intval', $days));

        if (empty($days)) {
            return;
        }

        $placeholders = implode(',', array_fill(0, count($days), '?'));
        $this->builder->whereRaw("(DAYOFWEEK(date) - 1) IN ({$placeholders})", $days);
    }
}
