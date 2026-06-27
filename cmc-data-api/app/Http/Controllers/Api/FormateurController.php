<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Filters\FormateurFilterRequest;
use App\Http\Resources\FormateurResource;
use App\Models\Formateur;
use App\Services\FormateurService;
use Illuminate\Http\Request;

class FormateurController
{
    public function __construct(private FormateurService $service) {}

    public function index(FormateurFilterRequest $request)
    {
        ['items' => $items] = $this->service->list($request);
        return FormateurResource::collection($items);
    }

    public function show(Request $request, Formateur $formateur)
    {
        return new FormateurResource($this->service->find($request, $formateur));
    }
}
