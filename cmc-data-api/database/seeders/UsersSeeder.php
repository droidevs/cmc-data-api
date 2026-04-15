<?php

namespace Database\Seeders;

use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Pole;
use App\Models\Stagiaire;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Formateurs per pole
        Pole::query()->get()->each(function (Pole $pole) {
            Formateur::factory()->count(8)->for($pole)->create();
        });

        // Stagiaires per groupe
        Groupe::query()->get()->each(function (Groupe $groupe) {
            Stagiaire::factory()->count(20)->for($groupe)->create();
        });
    }
}

