<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Affectation\StoreAffectationRequest;
use App\Http\Requests\Affectation\UpdateAffectationRequest;
use App\Http\Resources\AffectationResource;
use App\Models\Affectation;
use App\Services\AffectationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AffectationController
{
    public function __construct(private AffectationService $service) {}

    public function index(Request $request)
    {
        ['items' => $items] = $this->service->list($request);
        return AffectationResource::collection($items);
    }

    public function show(Request $request, Affectation $affectation)
    {
        return new AffectationResource($this->service->find($request, $affectation));
    }

    public function store(StoreAffectationRequest $request)
    {
        $affectation = $this->service->create($request->validated());

        return (new AffectationResource($affectation))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateAffectationRequest $request, Affectation $affectation)
    {
        return new AffectationResource($this->service->update($affectation, $request->validated()));
    }

    public function destroy(Affectation $affectation)
    {
        $this->service->delete($affectation);
        return response()->noContent();
    }
}
