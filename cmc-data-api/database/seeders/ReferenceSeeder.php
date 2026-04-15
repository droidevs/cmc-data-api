<?php

namespace Database\Seeders;

use App\Models\Niveau;
use App\Models\Pole;
use App\Models\TypeFormation;
use Illuminate\Database\Seeder;

class ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // Stable reference data (avoid duplicates on repeated runs)
        foreach (['1st Year', '2nd Year', '3rd Year', 'Specialization'] as $libelle) {
            Niveau::query()->firstOrCreate(['libelle' => $libelle]);
        }

        foreach (['Initial', 'Continuing', 'Apprenticeship', 'Evening'] as $libelle) {
            TypeFormation::query()->firstOrCreate(['libelle' => $libelle]);
        }

        foreach (['Digital', 'Industry', 'Services', 'Management'] as $libelle) {
            Pole::query()->firstOrCreate(['libelle' => $libelle]);
        }
    }
}

