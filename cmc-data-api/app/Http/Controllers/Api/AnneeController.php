<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Annee\StoreAnneeRequest;
use App\Http\Requests\Annee\UpdateAnneeRequest;
use App\Http\Resources\AnneeResource;
use App\Models\Annee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnneeController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['filiere', 'groupes', 'modules'];

    public function index(Request $request)
    {
        $query = Annee::query()->orderBy('id');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return AnneeResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Annee $annee)
    {
        $annee->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new AnneeResource($annee);
    }

    public function store(StoreAnneeRequest $request)
    {
        $annee = Annee::query()->create($request->validated());

        return (new AnneeResource($annee))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateAnneeRequest $request, Annee $annee)
    {
        $annee->update($request->validated());

        return new AnneeResource($annee);
    }

    public function destroy(Annee $annee)
    {
        $annee->delete();

        return response()->noContent();
    }
}

