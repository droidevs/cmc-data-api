<?php

declare(strict_types=1);

namespace App\Http\Requests\Filters;

class TimeRangeFilterRequest extends IndexFilterRequest
{
    /** Matches TimeRange's `datetime:H:i` cast format, e.g. "08:30", "14:00". */
    private const TIME_FORMAT = 'date_format:H:i';

    protected function filterRules(): array
    {
        return [
            'start_time' => ['sometimes', self::TIME_FORMAT],
            'end_time' => ['sometimes', self::TIME_FORMAT],
            'covers' => ['sometimes', self::TIME_FORMAT],
        ];
    }

    protected function sortable(): array
    {
        return ['start_time', 'end_time'];
    }

    protected function allowedIncludes(): array
    {
        return ['seances'];
    }
}
