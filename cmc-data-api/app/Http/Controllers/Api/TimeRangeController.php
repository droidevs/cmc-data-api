<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\TimeRangeFilterRequest;
use App\Http\Resources\TimeRangeResource;
use App\Models\TimeRange;
use App\Services\TimeRangeService;
use Illuminate\Http\Request;

/**
 * TimeRange is a reference table managed manually — read-only (index + show).
 * Delegates all query logic to TimeRangeService.
 */
class TimeRangeController
{
    public function __construct(private TimeRangeService $service) {}

    public function index(TimeRangeFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return TimeRangeResource::collection($items);
    }

    public function show(Request $request, TimeRange $timeRange)
    {
        return new TimeRangeResource($this->service->find($request, $timeRange));
    }
}
