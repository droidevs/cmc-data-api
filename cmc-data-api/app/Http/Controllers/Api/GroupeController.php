<?php

namespace App\Http\Controllers\Api;

use App\Filters\GroupeFilter;
use App\Http\Requests\Filters\GroupeFilterRequest;
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

    public function index(GroupeFilterRequest $request)
    {
        $query = Groupe::query()->orderBy('id');
        $this->withFilters($request, $query, GroupeFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return GroupeResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Groupe $groupe)
    {
        $groupe->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new GroupeResource($groupe);
    }
}
