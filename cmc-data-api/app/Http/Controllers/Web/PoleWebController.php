<?php

namespace App\Http\Controllers\Web;

use App\Models\Pole;
use App\Services\PoleService;
use Illuminate\Http\Request;

class PoleWebController extends WebController
{
    public function __construct(private PoleService $service) {}

    public function index(Request $request)
    {
        return view('poles.index', $this->service->list($request));
    }

    public function show(Request $request, Pole $pole)
    {
        return view('poles.show', ['pole' => $this->service->find($request, $pole)]);
    }
}
