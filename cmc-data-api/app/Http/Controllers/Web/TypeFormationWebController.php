<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Filters\TypeFormationFilterRequest;
use App\Models\TypeFormation;
use App\Services\TypeFormationService;
use Illuminate\Http\Request;

/**
 * TypeFormation is a small reference table — read-only (index + show),
 * mirroring Api\TypeFormationController / TypeFormationService.
 */
class TypeFormationWebController extends WebController
{
    public function __construct(private TypeFormationService $service) {}

    public function index(TypeFormationFilterRequest $request)
    {
        return view('type-formations.index', $this->service->list($request));
    }

    public function show(Request $request, TypeFormation $typeFormation)
    {
        return view('type-formations.show', ['typeFormation' => $this->service->find($request, $typeFormation)]);
    }
}
