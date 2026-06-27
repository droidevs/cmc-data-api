<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\AffectationFilter;
use App\Models\Affectation;

class AffectationService extends BaseService
{
    protected function modelClass(): string { return Affectation::class; }
    protected function filterClass(): string { return AffectationFilter::class; }

    protected function defaultWith(): array
    {
        return ['groupe', 'module', 'formateur'];
    }

    protected function defaultShowWith(): array
    {
        return [
            'groupe', 'groupe.annee', 'groupe.annee.filiere',
            'module', 'formateur', 'formateurSyn', 'seances', 'seances.timeRange', 'seances.espace',
        ];
    }

    protected function allowedIncludes(): array
    {
        return [
            'groupe', 'module', 'formateur', 'formateurSyn',
            'seances', 'seances.timeRange', 'seances.espace',
            'groupe.annee', 'groupe.annee.filiere',
        ];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Affectation::query()->orderBy('id');
    }

    /**
     * Create a new affectation (used by Web write controller).
     */
    public function create(array $validated): Affectation
    {
        $affectation = Affectation::create($validated);
        return $affectation->load(['groupe', 'module', 'formateur']);
    }

    /**
     * Update an existing affectation.
     */
    public function update(Affectation $affectation, array $validated): Affectation
    {
        $affectation->update($validated);
        return $affectation->load(['groupe', 'module', 'formateur']);
    }

    /**
     * Delete an affectation.
     */
    public function delete(Affectation $affectation): void
    {
        $affectation->delete();
    }
}
