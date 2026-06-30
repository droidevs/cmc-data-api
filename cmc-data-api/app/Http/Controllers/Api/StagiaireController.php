<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\StagiaireFilterRequest;
use App\Http\Resources\StagiaireResource;
use App\Models\Stagiaire;
use App\Services\StagiaireService;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

/**
 * Stagiaire data is sourced from lister_minimized.xlsx / BasePlateEvaluation.xlsx.
 * Read-only (index + show). Delegates to StagiaireService.
 */
class StagiaireController
{
    public function __construct(private StagiaireService $service) {}

    public function index(StagiaireFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);

        return StagiaireResource::collection($items);
    }

    public function show(Request $request, Stagiaire $stagiaire)
    {
        return new StagiaireResource($this->service->find($request, $stagiaire));
    }
}
