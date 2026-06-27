<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\PoleFilterRequest;
use App\Http\Resources\PoleResource;
use App\Models\Pole;
use App\Services\PoleService;
use Illuminate\Http\Request;

/**
 * Pole data is sourced entirely from Excel imports — no write endpoints.
 * Delegates all query logic to PoleService (shared with WebController).
 */
class PoleController
{
    public function __construct(private PoleService $service) {}

    public function index(PoleFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return PoleResource::collection($items);
    }

    public function show(Request $request, Pole $pole)
    {
        return new PoleResource($this->service->find($request, $pole));
    }
}
