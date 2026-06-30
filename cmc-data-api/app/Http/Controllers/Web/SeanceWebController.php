<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Filters\SeanceFilterRequest;
use App\Http\Requests\Seance\StoreSeanceRequest;
use App\Http\Requests\Seance\UpdateSeanceRequest;
use App\Models\Affectation;
use App\Models\Espace;
use App\Models\Seance;
use App\Models\TimeRange;
use App\Services\SeanceService;
use Illuminate\Http\Request;

class SeanceWebController extends WebController
{
    public function __construct(private SeanceService $service) {}

    public function index(SeanceFilterRequest $request)
    {
        return view('seances.index', $this->service->list($request));
    }

    public function show(Request $request, Seance $seance)
    {
        return view('seances.show', ['seance' => $this->service->find($request, $seance)]);
    }

    public function create()
    {
        return view('seances.create', [
            'affectations' => Affectation::with(['groupe', 'module'])->orderBy('id')->get(),
            'time_ranges'  => TimeRange::orderBy('start_time')->get(),
            'espaces'      => Espace::orderBy('libelle')->get(),
        ]);
    }

    public function store(StoreSeanceRequest $request)
    {
        $seance = $this->service->create($request->validated());

        return redirect()->route('web.seances.show', $seance)
            ->with('success', 'Séance créée avec succès.');
    }

    public function edit(Seance $seance)
    {
        return view('seances.edit', [
            'seance'       => $seance,
            'affectations' => Affectation::with(['groupe', 'module'])->orderBy('id')->get(),
            'time_ranges'  => TimeRange::orderBy('start_time')->get(),
            'espaces'      => Espace::orderBy('libelle')->get(),
        ]);
    }

    public function update(UpdateSeanceRequest $request, Seance $seance)
    {
        $this->service->update($seance, $request->validated());

        return redirect()->route('web.seances.show', $seance)
            ->with('success', 'Séance mise à jour.');
    }

    public function destroy(Seance $seance)
    {
        $this->service->delete($seance);

        return redirect()->route('web.seances.index')
            ->with('success', 'Séance supprimée.');
    }
}
