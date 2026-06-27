<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\EspaceFilter;
use App\Models\Espace;

class EspaceService extends BaseService
{
    protected function modelClass(): string { return Espace::class; }
    protected function filterClass(): string { return EspaceFilter::class; }

    protected function defaultWith(): array
    {
        return ['pole'];
    }

    protected function defaultShowWith(): array
    {
        return ['pole', 'seances', 'seances.affectation', 'seances.timeRange'];
    }

    protected function allowedIncludes(): array
    {
        return ['pole', 'seances'];
    }

    protected function baseQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Espace::query()->orderBy('id');
    }

    /**
     * Create a new espace (used by Web write controller).
     */
    public function create(array $validated): Espace
    {
        $espace = Espace::create($validated);
        return $espace->load(['pole']);
    }

    /**
     * Update an existing espace.
     */
    public function update(Espace $espace, array $validated): Espace
    {
        $espace->update($validated);
        return $espace->load(['pole']);
    }

    /**
     * Delete an espace.
     */
    public function delete(Espace $espace): void
    {
        $espace->delete();
    }
}
