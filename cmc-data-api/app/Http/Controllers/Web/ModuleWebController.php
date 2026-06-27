<?php

namespace App\Http\Controllers\Web;

use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Http\Request;

class ModuleWebController extends WebController
{
    public function __construct(private ModuleService $service) {}

    public function index(Request $request)
    {
        return view('modules.index', $this->service->list($request));
    }

    public function show(Request $request, Module $module)
    {
        return view('modules.show', ['module' => $this->service->find($request, $module)]);
    }
}
