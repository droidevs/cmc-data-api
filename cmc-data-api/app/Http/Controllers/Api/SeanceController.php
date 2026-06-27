<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Seance\StoreSeanceRequest;
use App\Http\Requests\Seance\UpdateSeanceRequest;
use App\Http\Resources\SeanceResource;
use App\Models\Seance;
use App\Services\SeanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeanceController
{
    public function __construct(private SeanceService $service) {}

    public function index(Request $request)
    {
        ['items' => $items] = $this->service->list($request);
        return SeanceResource::collection($items);
    }

    public function show(Request $request, Seance $seance)
    {
        return new SeanceResource($this->service->find($request, $seance));
    }

    public function store(StoreSeanceRequest $request)
    {
        $seance = $this->service->create($request->validated());

        return (new SeanceResource($seance))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateSeanceRequest $request, Seance $seance)
    {
        $this->service->update($seance, $request->validated());
        return new SeanceResource($seance);
    }

    public function destroy(Seance $seance)
    {
        $this->service->delete($seance);
        return response()->noContent();
    }
}
