<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Espace\StoreEspaceRequest;
use App\Http\Requests\Espace\UpdateEspaceRequest;
use App\Models\Espace;
use App\Models\Pole;
use App\Services\EspaceService;
use Illuminate\Http\Request;

class EspaceWebController extends WebController
{
    public function __construct(private EspaceService $service) {}

    public function index(Request $request)
    {
        return view('espaces.index', $this->service->list($request));
    }

    public function show(Request $request, Espace $espace)
    {
        return view('espaces.show', ['espace' => $this->service->find($request, $espace)]);
    }

    public function create()
    {
        return view('espaces.create', [
            'poles' => Pole::orderBy('libelle')->get(),
        ]);
    }

    public function store(StoreEspaceRequest $request)
    {
        $espace = $this->service->create($request->validated());

        return redirect()->route('web.espaces.show', $espace)
            ->with('success', 'Espace créé avec succès.');
    }

    public function edit(Espace $espace)
    {
        return view('espaces.edit', [
            'espace' => $espace,
            'poles'  => Pole::orderBy('libelle')->get(),
        ]);
    }

    public function update(UpdateEspaceRequest $request, Espace $espace)
    {
        $this->service->update($espace, $request->validated());

        return redirect()->route('web.espaces.show', $espace)
            ->with('success', 'Espace mis à jour.');
    }

    public function destroy(Espace $espace)
    {
        $this->service->delete($espace);

        return redirect()->route('web.espaces.index')
            ->with('success', 'Espace supprimé.');
    }
}
