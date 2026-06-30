<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Filters\StagiaireFilterRequest;
use App\Models\Stagiaire;
use App\Services\StagiaireService;
use Illuminate\Http\Request;

class StagiaireWebController extends WebController
{
    public function __construct(private StagiaireService $service) {}

    public function index(StagiaireFilterRequest $request)
    {
        return view('stagiaires.index', $this->service->list($request));
    }

    public function show(Request $request, Stagiaire $stagiaire)
    {
        return view('stagiaires.show', ['stagiaire' => $this->service->find($request, $stagiaire)]);
    }
}
