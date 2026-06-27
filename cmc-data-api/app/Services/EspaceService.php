<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\EspaceFilter;
use App\Models\Espace;

class EspaceService extends BaseService
{
    protected function modelClass(): string { return Espace::class; }
    protected function filterClass(): string { return EspaceFilter::class; }

    protected function defaultWith(): array
    {
        return ['pole'];
    }

    protected function defaultShowWith(): array
    {
        return ['pole', 'seances', 'seances.affectation', 'seances.timeRange'];
    }

    protected function allowedIncludes(): array
    {
        return ['pole', 'seances'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Espace::query()->orderBy('id');
    }
}
