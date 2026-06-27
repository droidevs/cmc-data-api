<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\NiveauFilterRequest;
use App\Http\Resources\NiveauResource;
use App\Models\Niveau;
use App\Services\NiveauService;
use Illuminate\Http\Request;

/**
 * Niveau is a small reference table — read-only (index + show).
 * Delegates to NiveauService.
 */
class NiveauController
{
    public function __construct(private NiveauService $service) {}

    public function index(NiveauFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return NiveauResource::collection($items);
    }

    public function show(Request $request, Niveau $niveau)
    {
        return new NiveauResource($this->service->find($request, $niveau));
    }
}
