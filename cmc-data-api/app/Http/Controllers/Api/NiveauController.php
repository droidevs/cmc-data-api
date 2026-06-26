<?php

namespace App\Http\Controllers\Api;

use App\Filters\NiveauFilter;
use App\Http\Resources\NiveauResource;
use App\Models\Niveau;
use Illuminate\Http\Request;

/**
 * Niveau is a small reference table sourced from Excel imports —
 * no write endpoints are exposed here. Only index/show remain.
 */
class NiveauController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['filieres'];

    public function index(Request $request)
    {
        $query = Niveau::query()->orderBy('id');
        $this->withFilters($request, $query, NiveauFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return NiveauResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Niveau $niveau)
    {
        $niveau->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new NiveauResource($niveau);
    }
}
