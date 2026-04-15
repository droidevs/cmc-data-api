<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Pole\StorePoleRequest;
use App\Http\Requests\Pole\UpdatePoleRequest;
use App\Http\Resources\PoleResource;
use App\Models\Pole;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PoleController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['espaces', 'formateurs', 'filieres'];

    public function index(Request $request)
    {
        $query = Pole::query()->orderBy('id');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return PoleResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Pole $pole)
    {
        $pole->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new PoleResource($pole);
    }

    public function store(StorePoleRequest $request)
    {
        $pole = Pole::query()->create($request->validated());

        return (new PoleResource($pole))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdatePoleRequest $request, Pole $pole)
    {
        $pole->update($request->validated());

        return new PoleResource($pole);
    }

    public function destroy(Pole $pole)
    {
        $pole->delete();

        return response()->noContent();
    }
}

