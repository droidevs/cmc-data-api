<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Affectation\StoreAffectationRequest;
use App\Http\Requests\Affectation\UpdateAffectationRequest;
use App\Http\Resources\AffectationResource;
use App\Models\Affectation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AffectationController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['groupe', 'module', 'formateur', 'seances', 'seances.timeRange'];

    public function index(Request $request)
    {
        $query = Affectation::query()->orderBy('id');
        // Default eager loading for this central pivot
        $query->with(['groupe', 'module', 'formateur']);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return AffectationResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Affectation $affectation)
    {
        $affectation->load(array_values(array_unique(array_merge(
            ['groupe', 'module', 'formateur'],
            $this->requestedIncludes($request, $this->allowedIncludes)
        ))));

        return new AffectationResource($affectation);
    }

    public function store(StoreAffectationRequest $request)
    {
        $affectation = Affectation::query()->create($request->validated());

        return (new AffectationResource($affectation->load(['groupe', 'module', 'formateur'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateAffectationRequest $request, Affectation $affectation)
    {
        $affectation->update($request->validated());

        return new AffectationResource($affectation->load(['groupe', 'module', 'formateur']));
    }

    public function destroy(Affectation $affectation)
    {
        $affectation->delete();

        return response()->noContent();
    }
}

