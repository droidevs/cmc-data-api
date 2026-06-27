<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\EspaceFilterRequest;
use App\Http\Resources\EspaceResource;
use App\Models\Espace;
use App\Services\EspaceService;
use Illuminate\Http\Request;

/**
 * Espace data is sourced entirely from Excel imports — read-only (index + show).
 * Delegates all query logic to EspaceService (shared with any future WebController).
 */
class EspaceController
{
    public function __construct(private EspaceService $service) {}

    public function index(EspaceFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return EspaceResource::collection($items);
    }

    public function show(Request $request, Espace $espace)
    {
        return new EspaceResource($this->service->find($request, $espace));
    }
}
