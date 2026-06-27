<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\TimeRangeFilter;
use App\Models\TimeRange;

class TimeRangeService extends BaseService
{
    protected function modelClass(): string { return TimeRange::class; }
    protected function filterClass(): string { return TimeRangeFilter::class; }

    protected function defaultWith(): array
    {
        return [];
    }

    protected function defaultShowWith(): array
    {
        return ['seances'];
    }

    protected function allowedIncludes(): array
    {
        return ['seances'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return TimeRange::query()->orderBy('start_time');
    }
}
