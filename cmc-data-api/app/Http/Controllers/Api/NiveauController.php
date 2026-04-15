<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Niveau\StoreNiveauRequest;
use App\Http\Requests\Niveau\UpdateNiveauRequest;
use App\Http\Resources\NiveauResource;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NiveauController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['filieres'];

    public function index(Request $request)
    {
        $query = Niveau::query()->orderBy('id');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return NiveauResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Niveau $niveau)
    {
        $niveau->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new NiveauResource($niveau);
    }

    public function store(StoreNiveauRequest $request)
    {
        $niveau = Niveau::query()->create($request->validated());

        return (new NiveauResource($niveau))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateNiveauRequest $request, Niveau $niveau)
    {
        $niveau->update($request->validated());

        return new NiveauResource($niveau);
    }

    public function destroy(Niveau $niveau)
    {
        $niveau->delete();

        return response()->noContent();
    }
}

