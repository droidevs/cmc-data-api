<?php

namespace App\Http\Controllers\Api;

use App\Filters\StagiaireFilter;
use App\Http\Resources\StagiaireResource;
use App\Models\Stagiaire;
use Illuminate\Http\Request;

/**
 * Stagiaire data is sourced from lister_minimized.xlsx and
 * BasePlateEvaluation.xlsx imports — no write endpoints here.
 * Only index/show remain.
 */
class StagiaireController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = [
        'groupe',
        'groupe.annee',
        'groupe.annee.filiere',
        'notes',
        'notes.seance',
        'notes.seance.affectation',
        'notes.seance.affectation.module',
    ];

    public function index(Request $request)
    {
        $query = Stagiaire::query()->orderBy('nom')->orderBy('prenom');
        $this->withFilters($request, $query, StagiaireFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return StagiaireResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Stagiaire $stagiaire)
    {
        $stagiaire->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new StagiaireResource($stagiaire);
    }
}
