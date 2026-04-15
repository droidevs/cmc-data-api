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
                ->each(function (Filiere $filiere) {
                    // 2 academic years
                    Annee::factory()
                        ->count(2)
                        ->for($filiere, 'filiere')
                        ->create()
                        ->each(function (Annee $annee) {
                            Groupe::factory()->count(3)->for($annee)->create();
                            Module::factory()->count(6)->for($annee)->create();
                        });
                });
        }
    }
}

