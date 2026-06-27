<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\NiveauFilter;
use App\Models\Niveau;

class NiveauService extends BaseService
{
    protected function modelClass(): string { return Niveau::class; }
    protected function filterClass(): string { return NiveauFilter::class; }

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
        return Niveau::query()->orderBy('id');
    }
}
