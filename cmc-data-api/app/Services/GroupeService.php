<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\GroupeFilter;
use App\Models\Groupe;

class GroupeService extends BaseService
{
    protected function modelClass(): string { return Groupe::class; }
    protected function filterClass(): string { return GroupeFilter::class; }

    protected function defaultWith(): array { return ['annee']; }

    protected function defaultShowWith(): array
    {
        return [
            'annee', 'annee.filiere', 'stagiaires',
            'affectations', 'affectations.module', 'affectations.formateur',
            'avancements', 'avancements.module',
        ];
    }

    protected function allowedIncludes(): array
    {
        return ['annee', 'annee.filiere', 'stagiaires', 'affectations', 'avancements', 'avancements.module'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Groupe::query()->orderBy('id');
    }
}
