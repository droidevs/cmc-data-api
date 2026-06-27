<?php

namespace App\Http\Controllers\Web;

use App\Models\Filiere;
use App\Services\FiliereService;
use Illuminate\Http\Request;

class FiliereWebController extends WebController
{
    public function __construct(private FiliereService $service) {}

    public function index(Request $request)
    {
        return view('filieres.index', $this->service->list($request));
    }

    public function show(Request $request, Filiere $filiere)
    {
        return view('filieres.show', ['filiere' => $this->service->find($request, $filiere)]);
    }
}
