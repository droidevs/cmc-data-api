<?php

namespace App\Http\Controllers\Api;

use App\Filters\EspaceFilter;
use App\Http\Requests\Filters\EspaceFilterRequest;
use App\Http\Resources\EspaceResource;
use App\Models\Espace;
use Illuminate\Http\Request;

/**
 * Espace data is sourced entirely from Excel imports — no write endpoints
 * are exposed here. Only index/show remain.
 */
class EspaceController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['pole', 'seances'];

    public function index(EspaceFilterRequest $request)
    {
        $query = Espace::query()->orderBy('id');
        $this->withFilters($request, $query, EspaceFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return EspaceResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Espace $espace)
    {
        $espace->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new EspaceResource($espace);
    }
}
