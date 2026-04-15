<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DateSeance\StoreDateSeanceRequest;
use App\Http\Requests\DateSeance\UpdateDateSeanceRequest;
use App\Http\Resources\DateSeanceResource;
use App\Models\DateSeance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DateSeanceController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['seance', 'seance.affectation'];

    public function index(Request $request)
    {
        $query = DateSeance::query()->orderBy('date');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return DateSeanceResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, DateSeance $dateSeance)
    {
        $dateSeance->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new DateSeanceResource($dateSeance);
    }

    public function store(StoreDateSeanceRequest $request)
    {
        $dateSeance = DateSeance::query()->create($request->validated());

        return (new DateSeanceResource($dateSeance))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateDateSeanceRequest $request, DateSeance $dateSeance)
    {
        $dateSeance->update($request->validated());

        return new DateSeanceResource($dateSeance);
    }

    public function destroy(DateSeance $dateSeance)
    {
        $dateSeance->delete();

        return response()->noContent();
    }
}

