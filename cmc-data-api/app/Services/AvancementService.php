<?php

declare(strict_types=1);

namespace App\Services;

use App\Filters\AvancementFilter;
use App\Models\Affectation;
use App\Models\Avancement;
use App\Models\Seance;
use Illuminate\Database\Eloquent\Builder;

class AvancementService extends BaseService
{
    protected function modelClass(): string { return Avancement::class; }
    protected function filterClass(): string { return AvancementFilter::class; }

    protected function defaultWith(): array
    {
        return ['groupe', 'module', 'module.annee.filiere'];
    }

    protected function defaultShowWith(): array
    {
        return ['groupe', 'groupe.annee.filiere', 'module', 'module.annee.filiere'];
    }

    protected function allowedIncludes(): array
    {
        return ['groupe', 'groupe.annee', 'groupe.annee.filiere', 'module', 'module.annee', 'module.annee.filiere'];
    }

    protected function baseQuery(): Builder
    {
        return Avancement::query()->orderBy('groupe_id')->orderBy('module_id');
    }

    public function refreshForSeance(Seance $seance): ?Avancement
    {
        $seance->loadMissing('affectation');

        if (! $seance->affectation) {
            return null;
        }

        return $this->refreshForAffectation($seance->affectation);
    }

    public function refreshForAffectation(Affectation $affectation): ?Avancement
    {
        if (! $affectation->groupe_id || ! $affectation->module_id) {
            return null;
        }

        $module = $affectation->module()->first();

        $seances = Seance::query()
            ->with('timeRange')
            ->whereHas('affectation', function (Builder $query) use ($affectation) {
                $query
                    ->where('groupe_id', $affectation->groupe_id)
                    ->where('module_id', $affectation->module_id);
            })
            ->get();

        $presentiel = 0.0;
        $syn = 0.0;

        foreach ($seances as $seance) {
            $hours = (float) ($seance->timeRange?->duration_hours ?? 0);

            if ($seance->espace_id === null) {
                $syn += $hours;
            } else {
                $presentiel += $hours;
            }
        }

        $total = round($presentiel + $syn, 2);
        $plannedPresentiel = (float) ($module?->mh_presentiel ?? 0);
        $plannedSyn = (float) ($module?->mh_syn ?? 0);
        $plannedTotal = (float) ($module?->mh_totale ?? 0);

        return Avancement::updateOrCreate(
            [
                'groupe_id' => $affectation->groupe_id,
                'module_id' => $affectation->module_id,
            ],
            [
                'mh_realisee_presentiel' => round($presentiel, 2),
                'mh_realisee_syn' => round($syn, 2),
                'mh_realisee_globale' => $total,
                'taux_realisation_presentiel' => $this->rate($presentiel, $plannedPresentiel),
                'taux_realisation_syn' => $this->rate($syn, $plannedSyn),
                'taux_realisation_globale' => $this->rate($total, $plannedTotal),
            ]
        );
    }

    private function rate(float $done, float $planned): float
    {
        if ($planned <= 0.0) {
            return 0.0;
        }

        return round(($done / $planned) * 100, 2);
    }
}
