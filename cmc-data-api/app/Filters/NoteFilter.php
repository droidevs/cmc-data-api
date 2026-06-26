<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for Note.
 *
 * Supported query params:
 *   seance_id          - exact or comma list
 *   stagiaire_cef      - exact or comma list
 *   type               - exact or comma list (cc, efm, exam)
 *   decision           - exact or comma list
 *   valeur_min/max     - grade range (0-20)
 *   passing            - 1 (valeur >= 10) | 0 (valeur < 10)
 *   missing            - 1 (valeur is null, i.e. not graded yet)
 *   groupe_id          - via stagiaire.groupe_id
 *   sort               - valeur|created_at (prefix "-" for desc)
 */
class NoteFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'seance_id' => 'filterSeanceId',
            'stagiaire_cef' => 'filterStagiaireCef',
            'type' => 'filterType',
            'decision' => 'filterDecision',
            'valeur_min' => 'filterValeurMin',
            'valeur_max' => 'filterValeurMax',
            'passing' => 'filterPassing',
            'missing' => 'filterMissing',
            'groupe_id' => 'filterGroupeId',
        ];
    }

    protected function sortable(): array
    {
        return ['valeur', 'created_at'];
    }

    protected function filterSeanceId(mixed $value): void
    {
        $this->inList('seance_id', $value);
    }

    protected function filterStagiaireCef(mixed $value): void
    {
        $this->inList('stagiaire_cef', $value);
    }

    protected function filterType(mixed $value): void
    {
        $this->inList('type', $value);
    }

    protected function filterDecision(mixed $value): void
    {
        $this->inList('decision', $value);
    }

    protected function filterValeurMin(mixed $value): void
    {
        $this->min('valeur', $value);
    }

    protected function filterValeurMax(mixed $value): void
    {
        $this->max('valeur', $value);
    }

    protected function filterPassing(mixed $value): void
    {
        if ($this->toBool($value)) {
            $this->builder->where('valeur', '>=', 10);
        } else {
            $this->builder->whereNotNull('valeur')->where('valeur', '<', 10);
        }
    }

    protected function filterMissing(mixed $value): void
    {
        if ($this->toBool($value)) {
            $this->builder->whereNull('valeur');
        } else {
            $this->builder->whereNotNull('valeur');
        }
    }

    protected function filterGroupeId(mixed $value): void
    {
        $this->builder->whereHas('stagiaire', fn ($q) => $q->where('groupe_id', $value));
    }
}
