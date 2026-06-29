<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\NoteFilter;
use App\Models\Note;
use App\Models\Seance;
use Illuminate\Validation\ValidationException;

class NoteService extends BaseService
{
    protected function modelClass(): string { return Note::class; }
    protected function filterClass(): string { return NoteFilter::class; }

    protected function defaultWith(): array
    {
        return ['seance.affectation.module', 'seance.affectation.groupe', 'seance.timeRange', 'stagiaire'];
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
        $validated['type'] = $this->resolveAndAssertType($validated);

        $note = Note::create($validated);
        return $note->load(['seance', 'stagiaire']);
    }

    public function update(Note $note, array $validated): Note
    {
        // seance_id may change on update; re-resolve against whichever
        // seance the note will end up attached to.
        $seanceId = $validated['seance_id'] ?? $note->seance_id;
        $validated['type'] = $this->resolveAndAssertType($validated, $seanceId);

        $note->update($validated);
        return $note->load(['seance', 'stagiaire']);
    }

    public function delete(Note $note): void
    {
        $note->delete();
    }

    /**
     * A Note's type always mirrors its parent Seance's type, and only
     * evaluable séance types (cc | efm | exam) may carry a Note at all.
     * This is the authoritative enforcement point — the Request classes
     * validate the same rule up front for a fast 422, but this method is
     * what actually decides the stored value and blocks the write.
     *
     * @param array<string, mixed> $validated
     */
    private function resolveAndAssertType(array $validated, ?int $seanceIdOverride = null): string
    {
        $seanceId = $seanceIdOverride ?? ($validated['seance_id'] ?? null);

        if (! $seanceId) {
            throw ValidationException::withMessages([
                'seance_id' => 'La séance est obligatoire pour déterminer le type de la note.',
            ]);
        }

        $seance = Seance::find($seanceId);

        if (! $seance) {
            throw ValidationException::withMessages([
                'seance_id' => 'Cette séance n\'existe pas.',
            ]);
        }

        if (! in_array($seance->type?->value ?? $seance->type, \App\Enums\NoteType::evaluable(), true)) {
            throw ValidationException::withMessages([
                'seance_id' => "Impossible de créer une note pour une séance de type \"{$seance->type?->value}\". ".
                    'Seules les séances cc ou efm peuvent recevoir des notes.',
            ]);
        }

        // The note's type is not a free choice — it always mirrors the
        // séance it belongs to, even if the client sent a different value.
        return $seance->type?->value ?? $seance->type;
    }
}
