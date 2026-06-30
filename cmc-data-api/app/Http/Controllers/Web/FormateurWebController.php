<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Filters\FormateurFilterRequest;
use App\Models\Formateur;
use App\Services\FormateurService;
use Illuminate\Http\Request;

class FormateurWebController extends WebController
{
    public function __construct(private FormateurService $service) {}

    public function index(FormateurFilterRequest $request)
    {
        return view('formateurs.index', $this->service->list($request));
    }

    public function show(Request $request, Formateur $formateur)
    {
        return view('formateurs.show', ['formateur' => $this->service->find($request, $formateur)]);
    }
}
