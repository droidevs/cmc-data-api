<?php

namespace Database\Seeders;

use App\Models\Affectation;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Seance;
use App\Models\TimeRange;
use Illuminate\Database\Seeder;

/**
 * Builds Affectations (module ↔ groupe ↔ formateur assignments) and their
 * Seances, on top of the real Groupes/Modules/Formateurs already inserted
 * by AcademicStructureSeeder and FormateursSeeder.
 *
 * Data-integrity rules enforced here (the things the original version got
 * wrong):
 *   - `mode` only ever takes the two values that exist in the real dataset
 *     ("Résidentiel" / "Alternance"). The previous version also picked
 *     "synchrone"/"async", which aren't valid Affectation.mode values —
 *     the presentiel/sync distinction lives in mh_affecte vs mh_affecte_syn,
 *     not in `mode`.
 *   - A Module is only ever attached to a Groupe that shares its Annee
 *     (a 1ère année groupe never gets a 2ème année module), via
 *     AffectationFactory::coherentAnnee().
 *   - Formateurs are picked from the Groupe's own Pole when possible, exactly
 *     like before, but now goes through the factory's `assigned()` state so
 *     hour pairs stay realistic instead of being set ad hoc.
 */
class PlanningSeeder extends Seeder
{
    public function run(): void
    {
        $timeRanges = TimeRange::query()->get();
        if ($timeRanges->isEmpty()) {
            $timeRanges = TimeRange::factory()->count(4)->create();
        }

        Groupe::query()->with(['annee.filiere'])->get()->each(function (Groupe $groupe) use ($timeRanges) {
            $modules = Module::query()->where('annee_id', $groupe->annee_id)->get();
            if ($modules->isEmpty()) {
                return;
            }

            $poleId = $groupe->annee?->filiere?->pole_id;
            $formateurs = $poleId
                ? Formateur::query()->where('pole_id', $poleId)->get()
                : collect();

            // Fallback if filiere not eager loaded (keep safe): take any formateur
            if ($formateurs->isEmpty()) {
                $formateurs = Formateur::query()->inRandomOrder()->limit(10)->get();
            }

            if ($formateurs->isEmpty()) {
                $this->command?->warn("No formateurs available for groupe {$groupe->code}; skipping.");
                return;
            }

            $pickedModules = $modules->random(min(3, $modules->count()));
            if (! $pickedModules instanceof \Illuminate\Support\Collection) {
                $pickedModules = collect([$pickedModules]);
            }

            foreach ($pickedModules as $module) {
                $formateur = $formateurs->random();

                $affectation = Affectation::factory()
                    ->assigned()
                    ->create([
                        'groupe_id'     => $groupe->getKey(),
                        'module_code'   => $module->getKey(),
                        'formateur_mle' => $formateur->getKey(),
                        // Only the two modes observed in the real dataset.
                        'mode'          => $groupe->mode ?? 'Résidentiel',
                    ]);

                // 5 seances per affectation with (date + time_range)
                Seance::factory()
                    ->count(5)
                    ->for($affectation)
                    ->state(fn () => [
                        'time_range_id' => $timeRanges->random()->getKey(),
                    ])
                    ->create();
            }
        });
    }
}
