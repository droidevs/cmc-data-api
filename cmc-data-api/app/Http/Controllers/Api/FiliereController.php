<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\FiliereFilterRequest;
use App\Http\Resources\FiliereResource;
use App\Models\Filiere;
use App\Services\FiliereService;
use Illuminate\Http\Request;

/**
 * Filiere data is sourced from AvancementProgramme.xlsx — read-only (index + show).
 * Delegates to FiliereService.
 */
class FiliereController
{
    public function __construct(private FiliereService $service) {}

    public function index(FiliereFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return FiliereResource::collection($items);
    }

    public function show(Request $request, Filiere $filiere)
    {
        return new FiliereResource($this->service->find($request, $filiere));
    }
}
