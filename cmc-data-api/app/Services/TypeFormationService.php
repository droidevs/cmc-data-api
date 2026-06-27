<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\TypeFormationFilter;
use App\Models\TypeFormation;

class TypeFormationService extends BaseService
{
    protected function modelClass(): string { return TypeFormation::class; }
    protected function filterClass(): string { return TypeFormationFilter::class; }

    protected function defaultWith(): array
    {
        return [];
    }

    protected function defaultShowWith(): array
    {
        return ['filieres'];
    }

    protected function allowedIncludes(): array
    {
        return ['filieres'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return TypeFormation::query()->orderBy('id');
    }
}
