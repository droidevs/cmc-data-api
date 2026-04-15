<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TypeFormation\StoreTypeFormationRequest;
use App\Http\Requests\TypeFormation\UpdateTypeFormationRequest;
use App\Http\Resources\TypeFormationResource;
use App\Models\TypeFormation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypeFormationController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['filieres'];

    public function index(Request $request)
    {
        $query = TypeFormation::query()->orderBy('id');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return TypeFormationResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, TypeFormation $typeFormation)
    {
        $typeFormation->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new TypeFormationResource($typeFormation);
    }

    public function store(StoreTypeFormationRequest $request)
    {
        $typeFormation = TypeFormation::query()->create($request->validated());

        return (new TypeFormationResource($typeFormation))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateTypeFormationRequest $request, TypeFormation $typeFormation)
    {
        $typeFormation->update($request->validated());

        return new TypeFormationResource($typeFormation);
    }

    public function destroy(TypeFormation $typeFormation)
    {
        $typeFormation->delete();

        return response()->noContent();
    }
}

