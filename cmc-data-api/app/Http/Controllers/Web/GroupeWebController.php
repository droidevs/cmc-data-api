<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Filters\GroupeFilterRequest;
use App\Models\Groupe;
use App\Services\GroupeService;
use Illuminate\Http\Request;

class GroupeWebController extends WebController
{
    public function __construct(private GroupeService $service) {}

    public function index(GroupeFilterRequest $request)
    {
        return view('groupes.index', $this->service->list($request));
    }

    public function show(Request $request, Groupe $groupe)
    {
        return view('groupes.show', ['groupe' => $this->service->find($request, $groupe)]);
    }
}
