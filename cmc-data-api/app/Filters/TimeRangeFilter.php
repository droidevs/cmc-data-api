<?php

declare(strict_types=1);

namespace App\Filters;

/**
 * Advanced filters for TimeRange.
 *
 * Supported query params:
 *   start_time / end_time   - exact "H:i" match
 *   covers                  - "H:i" time that must fall within [start_time, end_time]
 *   sort                    - start_time|end_time (prefix "-" for desc)
 */
class TimeRangeFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'start_time' => 'filterStartTime',
            'end_time' => 'filterEndTime',
            'covers' => 'filterCovers',
        ];
    }

    protected function sortable(): array
    {
        return ['start_time', 'end_time'];
    }

    protected function filterStartTime(mixed $value): void
    {
        $this->exact('start_time', $value);
    }

    protected function filterEndTime(mixed $value): void
    {
        $this->exact('end_time', $value);
    }

    protected function filterCovers(mixed $value): void
    {
        $this->builder->where('start_time', '<=', $value)->where('end_time', '>=', $value);
    }
}
