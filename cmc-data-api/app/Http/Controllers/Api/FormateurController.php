<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Formateur\StoreFormateurRequest;
use App\Http\Requests\Formateur\UpdateFormateurRequest;
use App\Http\Resources\FormateurResource;
use App\Models\Formateur;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormateurController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['pole', 'affectations'];

    public function index(Request $request)
    {
        $query = Formateur::query()->orderBy('nom_prenom');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return FormateurResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Formateur $formateur)
    {
        $formateur->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new FormateurResource($formateur);
    }

    public function store(StoreFormateurRequest $request)
    {
        $formateur = Formateur::query()->create($request->validated());

        return (new FormateurResource($formateur))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateFormateurRequest $request, Formateur $formateur)
    {
        $formateur->update($request->validated());

        return new FormateurResource($formateur);
    }

    public function destroy(Formateur $formateur)
    {
        $formateur->delete();

        return response()->noContent();
    }
}

