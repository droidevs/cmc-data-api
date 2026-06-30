<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Filters\TimeRangeFilterRequest;
use App\Http\Requests\TimeRange\StoreTimeRangeRequest;
use App\Http\Requests\TimeRange\UpdateTimeRangeRequest;
use App\Models\TimeRange;
use App\Services\TimeRangeService;
use Illuminate\Http\Request;

class TimeRangeWebController extends WebController
{
    public function __construct(private TimeRangeService $service) {}

    public function index(TimeRangeFilterRequest $request)
    {
        return view('time-ranges.index', $this->service->list($request));
    }

    public function show(Request $request, TimeRange $timeRange)
    {
        return view('time-ranges.show', ['timeRange' => $this->service->find($request, $timeRange)]);
    }

    public function create()
    {
        return view('time-ranges.create');
    }

    public function store(StoreTimeRangeRequest $request)
    {
        $timeRange = $this->service->create($request->validated());

        return redirect()->route('web.time-ranges.show', $timeRange)
            ->with('success', 'Créneau horaire créé avec succès.');
    }

    public function edit(TimeRange $timeRange)
    {
        return view('time-ranges.edit', ['timeRange' => $timeRange]);
    }

    public function update(UpdateTimeRangeRequest $request, TimeRange $timeRange)
    {
        $this->service->update($timeRange, $request->validated());

        return redirect()->route('web.time-ranges.show', $timeRange)
            ->with('success', 'Créneau horaire mis à jour.');
    }

    public function destroy(TimeRange $timeRange)
    {
        $this->service->delete($timeRange);

        return redirect()->route('web.time-ranges.index')
            ->with('success', 'Créneau horaire supprimé.');
    }
}
