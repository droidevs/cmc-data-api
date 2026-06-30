<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Filters\PoleFilterRequest;
use App\Models\Pole;
use App\Services\PoleService;
use Illuminate\Http\Request;

class PoleWebController extends WebController
{
    public function __construct(private PoleService $service) {}

    public function index(PoleFilterRequest $request)
    {
        return view('poles.index', $this->service->list($request));
    }

    public function show(Request $request, Pole $pole)
    {
        return view('poles.show', ['pole' => $this->service->find($request, $pole)]);
    }
}
