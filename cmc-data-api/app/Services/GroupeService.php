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
        return ['annee', 'stagiaires', 'affectations', 'affectations.module', 'affectations.formateur'];
    }

    protected function allowedIncludes(): array
    {
        return ['annee', 'stagiaires', 'affectations'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Groupe::query()->orderBy('id');
    }
}
