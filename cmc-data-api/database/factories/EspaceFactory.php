<?php

namespace Database\Factories;

use App\Models\Espace;
use App\Models\Pole;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Espace> */
class EspaceFactory extends Factory
{
    protected $model = Espace::class;

    public function definition(): array
    {
        return [
            'pole_id' => Pole::factory(),
            'libelle' => $this->faker->randomElement(['Salle', 'Atelier', 'Lab', 'Espace']) . ' ' . $this->faker->unique()->numberBetween(1, 60),
        ];
    }
}

