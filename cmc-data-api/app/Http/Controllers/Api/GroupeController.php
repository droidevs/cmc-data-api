<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\GroupeFilterRequest;
use App\Http\Resources\GroupeResource;
use App\Models\Groupe;
use App\Services\GroupeService;
use Illuminate\Http\Request;

/**
 * Groupe data is sourced from AvancementProgramme.xlsx — read-only (index + show).
 * Delegates to GroupeService.
 *
 * Note: StoreGroupeRequest / UpdateGroupeRequest exist in the codebase but the
 * architectural contract designates Groupe as read-only (Excel-sourced), so write
 * endpoints are intentionally not exposed here.
 */
class GroupeController
{
    public function __construct(private GroupeService $service) {}

    public function index(GroupeFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return GroupeResource::collection($items);
    }

    public function show(Request $request, Groupe $groupe)
    {
        return new GroupeResource($this->service->find($request, $groupe));
    }
}
