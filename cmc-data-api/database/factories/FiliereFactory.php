<?php

namespace Database\Factories;

use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Pole;
use App\Models\TypeFormation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Filiere> */
class FiliereFactory extends Factory
{
    protected $model = Filiere::class;

    public function definition(): array
    {
        $short = strtoupper(Str::random(3));

        return [
            'code_filiere' => $short . '-' . $this->faker->unique()->numberBetween(10, 9999),
            'pole_id' => Pole::factory(),
            'niveau_id' => Niveau::factory(),
            'type_formation_id' => TypeFormation::factory(),
            'libelle' => $this->faker->unique()->words(3, true),
        ];
    }
}

