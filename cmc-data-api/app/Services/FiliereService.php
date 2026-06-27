<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\FiliereFilter;
use App\Models\Filiere;

class FiliereService extends BaseService
{
    protected function modelClass(): string { return Filiere::class; }
    protected function filterClass(): string { return FiliereFilter::class; }

    protected function defaultWith(): array
    {
        return ['pole', 'niveau', 'typeFormation'];
    }

    protected function defaultShowWith(): array
    {
        return ['pole', 'niveau', 'typeFormation', 'annees'];
    }

    protected function allowedIncludes(): array
    {
        return ['pole', 'niveau', 'typeFormation', 'annees'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Filiere::query()->orderBy('libelle');
    }
}
