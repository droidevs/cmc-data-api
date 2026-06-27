<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\PoleFilter;
use App\Models\Pole;

class PoleService extends BaseService
{
    protected function modelClass(): string { return Pole::class; }
    protected function filterClass(): string { return PoleFilter::class; }

    protected function defaultWith(): array
    {
        return [];
    }

    protected function defaultShowWith(): array
    {
        return ['espaces', 'formateurs', 'filieres'];
    }

    protected function allowedIncludes(): array
    {
        return ['espaces', 'formateurs', 'filieres'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Pole::query()->orderBy('id');
    }
}
