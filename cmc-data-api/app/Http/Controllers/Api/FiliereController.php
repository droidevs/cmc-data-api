<?php

namespace App\Http\Controllers\Api;

use App\Filters\FiliereFilter;
use App\Http\Requests\Filters\FiliereFilterRequest;
use App\Http\Resources\FiliereResource;
use App\Models\Filiere;
use Illuminate\Http\Request;

/**
 * Filiere data is sourced entirely from AvancementProgramme.xlsx imports —
 * no write endpoints are exposed here. Only index/show remain.
 */
class FiliereController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['pole', 'niveau', 'typeFormation', 'annees'];

    public function index(FiliereFilterRequest $request)
    {
        $query = Filiere::query()->orderBy('libelle');
        $this->withFilters($request, $query, FiliereFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return FiliereResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Filiere $filiere)
    {
        $filiere->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new FiliereResource($filiere);
    }
}
