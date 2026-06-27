<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\AnneeFilterRequest;
use App\Http\Resources\AnneeResource;
use App\Models\Annee;
use App\Services\AnneeService;
use Illuminate\Http\Request;

/**
 * Annee data is sourced from AvancementProgramme.xlsx — read-only (index + show).
 * Delegates to AnneeService.
 */
class AnneeController
{
    public function __construct(private AnneeService $service) {}

    public function index(AnneeFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return AnneeResource::collection($items);
    }

    public function show(Request $request, Annee $annee)
    {
        return new AnneeResource($this->service->find($request, $annee));
    }
}
