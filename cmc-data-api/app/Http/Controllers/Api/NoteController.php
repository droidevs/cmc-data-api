<?php

namespace App\Http\Controllers\Api;

use App\Filters\NoteFilter;
use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NoteController extends ApiController
{
    /** @var array<int, string> */
    private array $allowedIncludes = [
        'seance',
        'seance.affectation',
        'seance.affectation.module',
        'seance.affectation.groupe',
        'seance.timeRange',
        'stagiaire',
        'stagiaire.groupe',
    ];

    public function index(Request $request)
    {
        $query = Note::query()->orderBy('id');
        $query->with(['seance', 'stagiaire']);
        $this->withFilters($request, $query, NoteFilter::class);
        $this->withRequestedIncludes($request, $query, $this->allowedIncludes);

        return NoteResource::collection($this->paginate($request, $query));
    }

    public function show(Request $request, Note $note)
    {
        $note->load(array_values(array_unique(array_merge(
            ['seance', 'stagiaire'],
            $this->requestedIncludes($request, $this->allowedIncludes)
        ))));

        return new NoteResource($note);
    }

    public function store(StoreNoteRequest $request)
    {
        $note = Note::query()->create($request->validated());

        return (new NoteResource($note->load(['seance', 'stagiaire'])))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        $note->update($request->validated());

        return new NoteResource($note->load(['seance', 'stagiaire']));
    }

    public function destroy(Note $note)
    {
        $note->delete();

        return response()->noContent();
    }
}
