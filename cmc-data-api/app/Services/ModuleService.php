<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\ModuleFilter;
use App\Models\Module;

class ModuleService extends BaseService
{
    protected function modelClass(): string { return Module::class; }
    protected function filterClass(): string { return ModuleFilter::class; }

    protected function defaultWith(): array { return ['annee']; }

    protected function defaultShowWith(): array
    {
        return [
            'annee', 'annee.filiere',
            'affectations', 'affectations.groupe', 'affectations.formateur',
            'avancements', 'avancements.groupe',
        ];
    }

    protected function allowedIncludes(): array
    {
        return [
            'annee', 'annee.filiere',
            'affectations', 'affectations.groupe', 'affectations.formateur',
            'avancements', 'avancements.groupe',
        ];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Module::query()->orderBy('libelle');
    }
}
