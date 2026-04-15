<?php

namespace Database\Seeders;

use App\Models\Affectation;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Seance;
use App\Models\TimeRange;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PlanningSeeder extends Seeder
{
    public function run(): void
    {
        Groupe::query()->with(['annee.filiere'])->get()->each(function (Groupe $groupe) {
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

            $pickedModules = $modules->random(min(3, $modules->count()));

            foreach ($pickedModules as $module) {
                $formateur = $formateurs->random();

                $affectation = Affectation::factory()
                    ->coherentAnnee()
                    ->create([
                        'groupe_id' => $groupe->getKey(),
                        'module_code' => $module->getKey(),
                        'formateur_mle' => $formateur->getKey(),
                        'mode' => Arr::random(['presentiel', 'synchrone', 'async']),
                    ]);

                $timeRanges = TimeRange::query()->get();
                if ($timeRanges->isEmpty()) {
                    $timeRanges = TimeRange::factory()->count(4)->create();
                }

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


