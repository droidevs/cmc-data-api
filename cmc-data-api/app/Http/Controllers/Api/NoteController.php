<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Note\StoreNoteRequest;
use App\Http\Requests\Note\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NoteController
{
    public function __construct(private NoteService $service) {}

    public function index(Request $request)
    {
        ['items' => $items] = $this->service->list($request);
        return NoteResource::collection($items);
    }

    public function show(Request $request, Note $note)
    {
        return new NoteResource($this->service->find($request, $note));
    }

    public function store(StoreNoteRequest $request)
    {
        $note = $this->service->create($request->validated());

        return (new NoteResource($note))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateNoteRequest $request, Note $note)
    {
        return new NoteResource($this->service->update($note, $request->validated()));
    }

    public function destroy(Note $note)
    {
        $this->service->delete($note);
        return response()->noContent();
    }
}
