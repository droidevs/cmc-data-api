<?php

namespace App\Http\Controllers\Web;

use App\Models\Niveau;
use App\Services\NiveauService;
use Illuminate\Http\Request;

/**
 * Niveau is a small reference table — read-only (index + show),
 * mirroring Api\NiveauController / NiveauService.
 */
class NiveauWebController extends WebController
{
    public function __construct(private NiveauService $service) {}

    public function index(Request $request)
    {
        return view('niveaux.index', $this->service->list($request));
    }

    public function show(Request $request, Niveau $niveau)
    {
        return view('niveaux.show', ['niveau' => $this->service->find($request, $niveau)]);
    }
}
