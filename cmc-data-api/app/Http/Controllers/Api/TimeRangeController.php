<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TimeRange\StoreTimeRangeRequest;
use App\Http\Requests\TimeRange\UpdateTimeRangeRequest;
use App\Http\Resources\TimeRangeResource;
use App\Models\TimeRange;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TimeRangeController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['seances'];

    public function index(Request $request)
    {
        $query = TimeRange::query()->orderBy('start_time');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return TimeRangeResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, TimeRange $timeRange)
    {
        $timeRange->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new TimeRangeResource($timeRange);
    }

    public function store(StoreTimeRangeRequest $request)
    {
        $timeRange = TimeRange::query()->create($request->validated());

        return (new TimeRangeResource($timeRange))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateTimeRangeRequest $request, TimeRange $timeRange)
    {
        $timeRange->update($request->validated());

        return new TimeRangeResource($timeRange);
    }

    public function destroy(TimeRange $timeRange)
    {
        $timeRange->delete();

        return response()->noContent();
    }
}

