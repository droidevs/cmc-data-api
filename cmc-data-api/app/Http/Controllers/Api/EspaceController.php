<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Espace\StoreEspaceRequest;
use App\Http\Requests\Espace\UpdateEspaceRequest;
use App\Http\Resources\EspaceResource;
use App\Models\Espace;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EspaceController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['pole'];

    public function index(Request $request)
    {
        $query = Espace::query()->orderBy('id');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return EspaceResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Espace $espace)
    {
        $espace->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new EspaceResource($espace);
    }

    public function store(StoreEspaceRequest $request)
    {
        $espace = Espace::query()->create($request->validated());

        return (new EspaceResource($espace))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateEspaceRequest $request, Espace $espace)
    {
        $espace->update($request->validated());

        return new EspaceResource($espace);
    }

    public function destroy(Espace $espace)
    {
        $espace->delete();

        return response()->noContent();
    }
}

