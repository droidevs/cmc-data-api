<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Filters\AvancementFilterRequest;
use App\Models\Avancement;
use App\Services\AvancementService;
use Illuminate\Http\Request;

class AvancementWebController extends WebController
{
    public function __construct(private AvancementService $service) {}

    public function index(AvancementFilterRequest $request)
    {
        return view('avancements.index', $this->service->list($request));
    }

    public function show(Request $request, Avancement $avancement)
    {
        return view('avancements.show', ['avancement' => $this->service->find($request, $avancement)]);
    }
}
