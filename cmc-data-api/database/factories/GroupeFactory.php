<?php

namespace Database\Factories;

use App\Models\Annee;
use App\Models\Groupe;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Groupe> */
class GroupeFactory extends Factory
{
    protected $model = Groupe::class;

    public function definition(): array
    {
        return [
            'annee_id' => Annee::factory(),
            'code' => $this->faker->unique()->bothify('G##-??'),
            'effectif' => $this->faker->numberBetween(12, 35),
        ];
    }
}

