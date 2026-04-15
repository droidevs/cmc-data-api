<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Groupe\StoreGroupeRequest;
use App\Http\Requests\Groupe\UpdateGroupeRequest;
use App\Http\Resources\GroupeResource;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroupeController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['annee', 'stagiaires', 'affectations'];

    public function index(Request $request)
    {
        $query = Groupe::query()->orderBy('id');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return GroupeResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Groupe $groupe)
    {
        $groupe->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new GroupeResource($groupe);
    }

    public function store(StoreGroupeRequest $request)
    {
        $groupe = Groupe::query()->create($request->validated());

        return (new GroupeResource($groupe))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateGroupeRequest $request, Groupe $groupe)
    {
        $groupe->update($request->validated());

        return new GroupeResource($groupe);
    }

    public function destroy(Groupe $groupe)
    {
        $groupe->delete();

        return response()->noContent();
    }
}

