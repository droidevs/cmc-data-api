<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\TypeFormationFilterRequest;
use App\Http\Resources\TypeFormationResource;
use App\Models\TypeFormation;
use App\Services\TypeFormationService;
use Illuminate\Http\Request;

/**
 * TypeFormation is a small reference table — read-only (index + show).
 * Delegates to TypeFormationService.
 */
class TypeFormationController
{
    public function __construct(private TypeFormationService $service) {}

    public function index(TypeFormationFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return TypeFormationResource::collection($items);
    }

    public function show(Request $request, TypeFormation $typeFormation)
    {
        return new TypeFormationResource($this->service->find($request, $typeFormation));
    }
}
