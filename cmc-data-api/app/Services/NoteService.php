<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\NoteFilter;
use App\Models\Note;

class NoteService extends BaseService
{
    protected function modelClass(): string { return Note::class; }
    protected function filterClass(): string { return NoteFilter::class; }

    protected function defaultWith(): array
    {
        return ['seance', 'stagiaire'];
    }

    protected function defaultShowWith(): array
    {
        return [
            'seance', 'seance.affectation', 'seance.affectation.module',
            'seance.affectation.groupe', 'seance.timeRange', 'stagiaire', 'stagiaire.groupe',
        ];
    }

    protected function allowedIncludes(): array
    {
        return [
            'seance', 'seance.affectation', 'seance.affectation.module',
            'seance.affectation.groupe', 'seance.timeRange', 'stagiaire', 'stagiaire.groupe',
        ];
    }
    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Note::query()->orderBy('id');
    }

    public function create(array $validated): Note
    {
        $note = Note::create($validated);
        return $note->load(['seance', 'stagiaire']);
    }

    public function update(Note $note, array $validated): Note
    {
        $note->update($validated);
        return $note->load(['seance', 'stagiaire']);
    }

    public function delete(Note $note): void
    {
        $note->delete();
    }
}
