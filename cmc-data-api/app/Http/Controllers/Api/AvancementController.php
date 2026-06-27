<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\AvancementFilterRequest;
use App\Http\Resources\AvancementResource;
use App\Models\Avancement;
use App\Services\AvancementService;
use Illuminate\Http\Request;

class AvancementController
{
    public function __construct(private AvancementService $service) {}

    public function index(AvancementFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return AvancementResource::collection($items);
    }

    public function show(Request $request, Avancement $avancement)
    {
        return new AvancementResource($this->service->find($request, $avancement));
    }
}
