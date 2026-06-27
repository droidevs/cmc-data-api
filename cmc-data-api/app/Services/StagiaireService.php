<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\StagiaireFilter;
use App\Models\Stagiaire;

class StagiaireService extends BaseService
{
    protected function modelClass(): string { return Stagiaire::class; }
    protected function filterClass(): string { return StagiaireFilter::class; }

    protected function defaultWith(): array
    {
        return ['groupe'];
    }

    protected function defaultShowWith(): array
    {
        return ['groupe', 'groupe.annee', 'groupe.annee.filiere', 'notes', 'notes.seance', 'notes.seance.affectation', 'notes.seance.affectation.module'];
    }

    protected function allowedIncludes(): array
    {
        return [
            'groupe',
            'groupe.annee',
            'groupe.annee.filiere',
            'notes',
            'notes.seance',
            'notes.seance.affectation',
            'notes.seance.affectation.module',
        ];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Stagiaire::query()->orderBy('nom')->orderBy('prenom');
    }
}
