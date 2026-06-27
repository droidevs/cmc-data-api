<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Models\Note;
use App\Models\Seance;
use App\Models\Stagiaire;
use App\Services\NoteService;
use Illuminate\Http\Request;

class NoteWebController extends WebController
{
    public function __construct(private NoteService $service) {}

    public function index(Request $request)
    {
        return view('notes.index', $this->service->list($request));
    }

    public function show(Request $request, Note $note)
    {
        return view('notes.show', ['note' => $this->service->find($request, $note)]);
    }

    public function create()
    {
        return view('notes.create', [
            'seances'    => Seance::with(['affectation.module', 'affectation.groupe'])->orderBy('date', 'desc')->get(),
            'stagiaires' => Stagiaire::orderBy('nom')->get(),
        ]);
    }

    public function store(StoreNoteRequest $request)
    {
        $note = $this->service->create($request->validated());

        return redirect()->route('web.notes.show', $note)
            ->with('success', 'Note enregistrée avec succès.');
    }

    public function edit(Note $note)
    {
        return view('notes.edit', [
            'note'       => $note->load(['seance', 'stagiaire']),
            'seances'    => Seance::with(['affectation.module'])->orderBy('date', 'desc')->get(),
            'stagiaires' => Stagiaire::orderBy('nom')->get(),
        ]);
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $this->service->update($note, $request->validated());

        return redirect()->route('web.notes.show', $note)
            ->with('success', 'Note mise à jour.');
    }

    public function destroy(Note $note)
    {
        $this->service->delete($note);

        return redirect()->route('web.notes.index')
            ->with('success', 'Note supprimée.');
    }
}
