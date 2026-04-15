<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filiere\StoreFiliereRequest;
use App\Http\Requests\Filiere\UpdateFiliereRequest;
use App\Http\Resources\FiliereResource;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FiliereController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['pole', 'niveau', 'typeFormation', 'annees'];

    public function index(Request $request)
    {
        $query = Filiere::query()->orderBy('libelle');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return FiliereResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Filiere $filiere)
    {
        $filiere->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new FiliereResource($filiere);
    }

    public function store(StoreFiliereRequest $request)
    {
        $filiere = Filiere::query()->create($request->validated());

        return (new FiliereResource($filiere))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateFiliereRequest $request, Filiere $filiere)
    {
        $filiere->update($request->validated());

        return new FiliereResource($filiere);
    }

    public function destroy(Filiere $filiere)
    {
        $filiere->delete();

        return response()->noContent();
    }
}

