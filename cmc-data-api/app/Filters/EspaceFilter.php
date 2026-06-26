<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Espace.
 *
 * Supported query params:
 *   q                - free text search (libelle)
 *   libelle           - partial match
 *   pole_id           - exact or comma list
 *   capacite_min/max  - capacity range
 *   effectif          - shorthand for "capacite IS NULL OR capacite >= effectif"
 *                       (mirrors Espace::scopeWithCapacityFor)
 *   available_date / available_time_range_id
 *                     - when BOTH are present, excludes espaces already booked
 *                       for that date + time range (mirrors Espace::isAvailable)
 *   sort              - libelle|capacite (prefix "-" for desc)
 */
class EspaceFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'q' => 'search',
            'libelle' => 'filterLibelle',
            'pole_id' => 'filterPoleId',
            'capacite_min' => 'filterCapaciteMin',
            'capacite_max' => 'filterCapaciteMax',
            'effectif' => 'filterEffectif',
            'available_date' => 'filterAvailableDate',
        ];
    }

    protected function sortable(): array
    {
        return ['libelle', 'capacite', 'created_at'];
    }

    protected function search(mixed $value): void
    {
        $this->like('libelle', $value);
    }

    protected function filterLibelle(mixed $value): void
    {
        $this->like('libelle', $value);
    }

    protected function filterPoleId(mixed $value): void
    {
        $this->inList('pole_id', $value);
    }

    protected function filterCapaciteMin(mixed $value): void
    {
        $this->min('capacite', $value);
    }

    protected function filterCapaciteMax(mixed $value): void
    {
        $this->max('capacite', $value);
    }

    protected function filterEffectif(mixed $value): void
    {
        $effectif = (int) $value;
        $this->builder->where(function ($q) use ($effectif) {
            $q->whereNull('capacite')->orWhere('capacite', '>=', $effectif);
        });
    }

    /**
     * Requires both available_date and available_time_range_id query params.
     * Excludes espaces that already have a seance on that date + time range.
     */
    protected function filterAvailableDate(mixed $value): void
    {
        $timeRangeId = $this->request->query('available_time_range_id');
        if ($timeRangeId === null) {
            return;
        }

        $this->builder->whereDoesntHave('seances', function ($q) use ($value, $timeRangeId) {
            $q->whereDate('date', $value)->where('time_range_id', $timeRangeId);
        });
    }
}
