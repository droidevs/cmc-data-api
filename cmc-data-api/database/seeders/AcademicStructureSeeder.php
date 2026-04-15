<?php

namespace Database\Seeders;

use App\Models\Annee;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\Pole;
use App\Models\TypeFormation;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        $poles = Pole::query()->get();
        $niveaux = Niveau::query()->get();
        $types = TypeFormation::query()->get();

        // Options are intentionally not stored in a dedicated table.
        // They are encoded into `annees.libelle`.
        $optionsPool = ['Web', 'Mobile', 'Cyber', 'Data', 'AI', 'DevOps'];

        // Create a few filieres per pole, each with annees, groupes, and modules.
        foreach ($poles as $pole) {
            Filiere::factory()
                ->count(2)
                ->for($pole)
                ->state(function () use ($niveaux, $types) {
                    return [
                        'niveau_id' => $niveaux->random()->getKey(),
                        'type_formation_id' => $types->random()->getKey(),
                    ];
                })
                ->create()
                ->each(function (Filiere $filiere) use ($optionsPool) {
                    // Rule requested:
                    // - 1ère année is common (Tronc commun)
                    // - 2ème année is created PER option
                    // This way 1 filiere can have 6+ "years" rows when it has many options.

                    $commonYear = Annee::factory()
                        ->firstYearCommon()
                        ->for($filiere, 'filiere')
                        ->create();

                    Groupe::factory()->count(3)->for($commonYear)->create();
                    Module::factory()->count(6)->for($commonYear)->create();

                    // Choose number of options for this filiere (at least 2) -> can scale higher.
                    $optionsCount = min(count($optionsPool), 3);
                    $options = collect($optionsPool)->shuffle()->take($optionsCount)->values();

                    $options->each(function (string $option) use ($filiere) {
                        $optionYear = Annee::factory()
                            ->optionSecondYear($option)
                            ->for($filiere, 'filiere')
                            ->create();

                        Groupe::factory()->count(3)->for($optionYear)->create();
                        Module::factory()->count(6)->for($optionYear)->create();
                    });
                });
        }
    }
}

