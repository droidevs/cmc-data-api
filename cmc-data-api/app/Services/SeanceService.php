<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\SeanceFilter;
use App\Models\Affectation;
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

    /**
     * Create a new affectation (used by Web write controller).
     */
    public function create(array $validated): Affectation
    {
        $seance = Seance::create($validated);
        return $seance->load(['affectation', 'timeRange', 'espace']);
    }

    /**
     * Update an existing affectation.
     */
    public function update(Seance $seance, array $validated): Affectation
    {
        $seance->update($validated);
        return $seance->load(['affectation', 'timeRange', 'espace']);
    }

    /**
     * Delete an affectation.
     */
    public function delete(Seance $seance): void
    {
        $seance->delete();
    }
}
