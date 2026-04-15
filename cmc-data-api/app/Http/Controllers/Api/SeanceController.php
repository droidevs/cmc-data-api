<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Seance\StoreSeanceRequest;
use App\Http\Requests\Seance\UpdateSeanceRequest;
use App\Http\Resources\SeanceResource;
use App\Models\Seance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeanceController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['affectation', 'dateSeance', 'notes', 'notes.stagiaire'];

    public function index(Request $request)
    {
        $query = Seance::query()->orderBy('id');
        $query->with(['affectation', 'dateSeance']);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return SeanceResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Seance $seance)
    {
        $seance->load(array_values(array_unique(array_merge(
            ['affectation', 'dateSeance'],
            $this->requestedIncludes($request, $this->allowedIncludes)
        ))));

        return new SeanceResource($seance);
    }

    public function store(StoreSeanceRequest $request)
    {
        $seance = Seance::query()->create($request->validated());

        return (new SeanceResource($seance->load(['affectation', 'dateSeance'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateSeanceRequest $request, Seance $seance)
    {
        $seance->update($request->validated());

        return new SeanceResource($seance->load(['affectation', 'dateSeance']));
    }

    public function destroy(Seance $seance)
    {
        $seance->delete();

        return response()->noContent();
    }
}

