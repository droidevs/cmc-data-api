<?php

namespace App\Http\Controllers\Api;

use App\Filters\AnneeFilter;
use App\Http\Resources\AnneeResource;
use App\Models\Annee;
use Illuminate\Http\Request;

/**
 * Annee data is sourced entirely from AvancementProgramme.xlsx imports —
 * no write endpoints are exposed here. Only index/show remain.
 */
class AnneeController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['filiere', 'groupes', 'modules'];

    public function index(Request $request)
    {
        $query = Annee::query()->orderBy('id');
        $this->withFilters($request, $query, AnneeFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return AnneeResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Annee $annee)
    {
        $annee->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new AnneeResource($annee);
    }
}
