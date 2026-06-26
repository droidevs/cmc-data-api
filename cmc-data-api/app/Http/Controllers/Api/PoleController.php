<?php

namespace App\Http\Controllers\Api;

use App\Filters\PoleFilter;
use App\Http\Resources\PoleResource;
use App\Models\Pole;
use Illuminate\Http\Request;

/**
 * Pole data is sourced entirely from Excel imports — no write endpoints
 * are exposed here. Only index/show remain.
 */
class PoleController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['espaces', 'formateurs', 'filieres'];

    public function index(Request $request)
    {
        $query = Pole::query()->orderBy('id');
        $this->withFilters($request, $query, PoleFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return PoleResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Pole $pole)
    {
        $pole->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new PoleResource($pole);
    }
}
