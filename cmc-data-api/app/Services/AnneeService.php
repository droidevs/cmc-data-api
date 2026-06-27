<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\AnneeFilter;
use App\Models\Annee;

class AnneeService extends BaseService
{
    protected function modelClass(): string { return Annee::class; }
    protected function filterClass(): string { return AnneeFilter::class; }

    protected function defaultWith(): array
    {
        return ['filiere'];
    }

    protected function defaultShowWith(): array
    {
        return ['filiere', 'groupes', 'modules'];
    }

    protected function allowedIncludes(): array
    {
        return ['filiere', 'groupes', 'modules'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Annee::query()->orderBy('id');
    }
}
