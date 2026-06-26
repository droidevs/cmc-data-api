<?php

namespace App\Http\Controllers\Api;

use App\Filters\TimeRangeFilter;
use App\Http\Requests\Filters\TimeRangeFilterRequest;
use App\Http\Resources\TimeRangeResource;
use App\Models\TimeRange;
use Illuminate\Http\Request;

/**
 * TimeRange is a reference table managed manually —
 * no write endpoints are exposed here. Only index/show remain.
 */
class TimeRangeController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['seances'];

    public function index(TimeRangeFilterRequest $request)
    {
        $query = TimeRange::query()->orderBy('start_time');
        $this->withFilters($request, $query, TimeRangeFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return TimeRangeResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, TimeRange $timeRange)
    {
        $timeRange->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new TimeRangeResource($timeRange);
    }
}
