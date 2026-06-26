<?php

namespace App\Http\Controllers\Api;

use App\Filters\ModuleFilter;
use App\Http\Requests\Filters\ModuleFilterRequest;
use App\Http\Resources\ModuleResource;
use App\Models\Module;
use Illuminate\Http\Request;

/**
 * Module data is sourced from AvancementProgramme.xlsx imports —
 * no write endpoints are exposed here. Only index/show remain.
 */
class ModuleController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = [
        'annee',
        'annee.filiere',
        'affectations',
        'affectations.groupe',
        'affectations.formateur',
    ];

    public function index(ModuleFilterRequest $request)
    {
        $query = Module::query()->orderBy('libelle');
        $this->withFilters($request, $query, ModuleFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return ModuleResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Module $module)
    {
        $module->load($this->requestedIncludes($request, $this->allowedIncludes));

        return new ModuleResource($module);
    }
}
