<?php

namespace App\Http\Controllers\Api;

use App\Filters\TypeFormationFilter;
use App\Http\Requests\Filters\TypeFormationFilterRequest;
use App\Http\Resources\TypeFormationResource;
use App\Models\TypeFormation;
use Illuminate\Http\Request;

/**
 * TypeFormation is a small reference table sourced from Excel imports —
 * no write endpoints are exposed here. Only index/show remain.
 */
class TypeFormationController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['filieres'];

    public function index(TypeFormationFilterRequest $request)
    {
        $query = TypeFormation::query()->orderBy('id');
        $this->withFilters($request, $query, TypeFormationFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return TypeFormationResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, TypeFormation $typeFormation)
    {
        $typeFormation->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new TypeFormationResource($typeFormation);
    }
}
