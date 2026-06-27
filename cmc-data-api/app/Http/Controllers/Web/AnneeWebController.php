<?php

namespace App\Http\Controllers\Web;

use App\Models\Annee;
use App\Services\AnneeService;
use Illuminate\Http\Request;

/**
 * Annee is sourced from AvancementProgramme.xlsx — read-only (index + show),
 * mirroring Api\AnneeController / AnneeService.
 */
class AnneeWebController extends WebController
{
    public function __construct(private AnneeService $service) {}

    public function index(Request $request)
    {
        return view('annees.index', $this->service->list($request));
    }

    public function show(Request $request, Annee $annee)
    {
        return view('annees.show', ['annee' => $this->service->find($request, $annee)]);
    }
}
