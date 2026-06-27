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

    /**
     * Create a new time range (used by Web write controller).
     */
    public function create(array $validated): TimeRange
    {
        return TimeRange::create($validated);
    }

    /**
     * Update an existing time range.
     */
    public function update(TimeRange $timeRange, array $validated): TimeRange
    {
        $timeRange->update($validated);
        return $timeRange;
    }

    /**
     * Delete a time range.
     */
    public function delete(TimeRange $timeRange): void
    {
        $timeRange->delete();
    }
}
