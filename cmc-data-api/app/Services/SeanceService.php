<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\SeanceFilter;
use App\Models\Seance;

class SeanceService extends BaseService
{
    protected function modelClass(): string { return Seance::class; }
    protected function filterClass(): string { return SeanceFilter::class; }

    protected function defaultWith(): array
    {
        return ['affectation', 'timeRange', 'espace'];
    }

    protected function defaultShowWith(): array
    {
        return [
            'affectation', 'affectation.groupe', 'affectation.module', 'affectation.formateur',
            'timeRange', 'espace', 'espace.pole', 'notes', 'notes.stagiaire',
        ];
    }

    protected function allowedIncludes(): array
    {
        return [
            'affectation', 'affectation.groupe', 'affectation.module', 'affectation.formateur',
            'timeRange', 'espace', 'espace.pole', 'notes', 'notes.stagiaire',
        ];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Seance::query()->orderBy('date')->orderBy('time_range_id');
    }

    public function create(array $validated): Seance
    {
        $seance = Seance::create($validated);
        return $seance->load(['affectation', 'timeRange', 'espace']);
    }

    public function update(Seance $seance, array $validated): Seance
    {
        $seance->update($validated);
        return $seance->load(['affectation', 'timeRange', 'espace']);
    }

    public function delete(Seance $seance): void
    {
        $seance->delete();
    }
}
