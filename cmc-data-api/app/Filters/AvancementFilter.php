<?php

declare(strict_types=1);

namespace App\Filters;

class AvancementFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'groupe_id' => 'filterGroupeId',
            'module_id' => 'filterModuleId',
            'filiere_code' => 'filterFiliereCode',
            'termine' => 'filterTermine',
        ];
    }

    protected function sortable(): array
    {
        return ['groupe_id', 'module_id', 'mh_realisee_globale', 'taux_realisation_globale', 'created_at'];
    }

    protected function filterGroupeId(mixed $value): void
    {
        $this->inList('groupe_id', $value);
    }

    protected function filterModuleId(mixed $value): void
    {
        $this->inList('module_id', $value);
    }

    protected function filterFiliereCode(mixed $value): void
    {
        $this->builder->whereHas('groupe.annee', fn ($query) => $query->where('filiere_code', $value));
    }

    protected function filterTermine(mixed $value): void
    {
        if ($this->toBool($value)) {
            $this->builder->where('taux_realisation_globale', '>=', 100);
        } else {
            $this->builder->where('taux_realisation_globale', '<', 100);
        }
    }
}
