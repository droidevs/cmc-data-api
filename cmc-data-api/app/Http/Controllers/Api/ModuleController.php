<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\ModuleFilterRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\Request;

/**
 * Module data is sourced from AvancementProgramme.xlsx — read-only (index + show).
 * Delegates all query logic to ModuleService.
 */
class ModuleController
{
    public function __construct(private ModuleService $service) {}

    public function index(ModuleFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return ModuleResource::collection($items);
    }

    public function show(Request $request, Module $module)
    {
        return new ModuleResource($this->service->find($request, $module));
    }
}
