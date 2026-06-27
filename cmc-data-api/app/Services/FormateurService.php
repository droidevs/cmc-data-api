<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\FormateurFilter;
use App\Models\Formateur;

class FormateurService extends BaseService
{
    protected function modelClass(): string { return Formateur::class; }
    protected function filterClass(): string { return FormateurFilter::class; }

    protected function defaultWith(): array
    {
        // 'pole' (singular) — Formateur::pole() is a BelongsTo, not 'poles'
        return ['pole'];
    }

    protected function defaultShowWith(): array
    {
        return ['pole', 'affectations', 'affectations.module', 'affectations.groupe'];
    }

    protected function allowedIncludes(): array
    {
        return ['pole', 'affectations', 'affectations.module', 'affectations.groupe'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Formateur::query()->orderBy('nom_prenom');
    }
}
