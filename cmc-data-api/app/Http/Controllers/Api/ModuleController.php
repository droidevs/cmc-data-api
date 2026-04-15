<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Module\StoreModuleRequest;
use App\Http\Requests\Module\UpdateModuleRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ModuleController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = ['annee', 'affectations'];

    public function index(Request $request)
    {
        $query = Module::query()->orderBy('libelle');
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return ModuleResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Module $module)
    {
        $module->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new ModuleResource($module);
    }

    public function store(StoreModuleRequest $request)
    {
        $module = Module::query()->create($request->validated());

        return (new ModuleResource($module))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateModuleRequest $request, Module $module)
    {
        $module->update($request->validated());

        return new ModuleResource($module);
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return response()->noContent();
    }
}

