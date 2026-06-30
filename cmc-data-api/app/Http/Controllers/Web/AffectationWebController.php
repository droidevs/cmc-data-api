<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Affectation\StoreAffectationRequest;
use App\Http\Requests\Affectation\UpdateAffectationRequest;
use App\Http\Requests\Filters\AffectationFilterRequest;
use App\Models\Affectation;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Module;
use App\Services\AffectationService;
use Illuminate\Http\Request;

class AffectationWebController extends WebController
{
    public function __construct(private AffectationService $service) {}

    public function index(AffectationFilterRequest $request)
    {
        return view('affectations.index', $this->service->list($request));
    }

    public function show(Request $request, Affectation $affectation)
    {
        return view('affectations.show', ['affectation' => $this->service->find($request, $affectation)]);
    }

    public function create()
    {
        return view('affectations.create', [
            'groupes'    => Groupe::with('annee')->orderBy('code')->get(),
            'modules'    => Module::orderBy('libelle')->get(),
            'formateurs' => Formateur::orderBy('nom_prenom')->get(),
        ]);
    }

    public function store(StoreAffectationRequest $request)
    {
        $affectation = $this->service->create($request->validated());

        return redirect()->route('web.affectations.show', $affectation)
            ->with('success', 'Affectation créée avec succès.');
    }

    public function edit(Affectation $affectation)
    {
        return view('affectations.edit', [
            'affectation' => $affectation,
            'groupes'     => Groupe::with('annee')->orderBy('code')->get(),
            'modules'     => Module::orderBy('libelle')->get(),
            'formateurs'  => Formateur::orderBy('nom_prenom')->get(),
        ]);
    }

    public function update(UpdateAffectationRequest $request, Affectation $affectation)
    {
        $this->service->update($affectation, $request->validated());

        return redirect()->route('web.affectations.show', $affectation)
            ->with('success', 'Affectation mise à jour.');
    }

    public function destroy(Affectation $affectation)
    {
        $this->service->delete($affectation);

        return redirect()->route('web.affectations.index')
            ->with('success', 'Affectation supprimée.');
    }
}
