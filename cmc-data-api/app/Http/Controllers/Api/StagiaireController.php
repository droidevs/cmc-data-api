<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Stagiaire\StoreStagiaireRequest;
use App\Http\Requests\Stagiaire\UpdateStagiaireRequest;
use App\Http\Resources\StagiaireResource;
use App\Models\Stagiaire;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StagiaireController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['groupe', 'notes', 'notes.seance'];

    public function index(Request $request)
    {
        $query = Stagiaire::query()->orderBy('nom')->orderBy('prenom');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return StagiaireResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Stagiaire $stagiaire)
    {
        $stagiaire->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new StagiaireResource($stagiaire);
    }

    public function store(StoreStagiaireRequest $request)
    {
        $stagiaire = Stagiaire::query()->create($request->validated());

        return (new StagiaireResource($stagiaire))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateStagiaireRequest $request, Stagiaire $stagiaire)
    {
        $stagiaire->update($request->validated());

        return new StagiaireResource($stagiaire);
    }

    public function destroy(Stagiaire $stagiaire)
    {
        $stagiaire->delete();

        return response()->noContent();
    }
}

