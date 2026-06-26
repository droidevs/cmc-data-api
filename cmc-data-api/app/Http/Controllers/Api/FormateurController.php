<?php

namespace App\Http\Controllers\Api;

use App\Filters\FormateurFilter;
use App\Http\Requests\Filters\FormateurFilterRequest;
use App\Http\Resources\FormateurResource;
use App\Models\Formateur;
use Illuminate\Http\Request;

/**
 * Formateur data is sourced entirely from Base_Formateurs.xlsx imports —
 * no write endpoints are exposed here. Only index/show remain.
 */
class FormateurController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['pole', 'affectations', 'affectations.module', 'affectations.groupe'];

    public function index(FormateurFilterRequest $request)
    {
        $query = Formateur::query()->orderBy('nom_prenom');
        $this->withFilters($request, $query, FormateurFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return FormateurResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Formateur $formateur)
    {
        $formateur->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new FormateurResource($formateur);
    }
}
