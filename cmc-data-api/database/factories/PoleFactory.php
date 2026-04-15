<?php

namespace Database\Factories;

use App\Models\Pole;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Pole> */
class PoleFactory extends Factory
{
    protected $model = Pole::class;

    public function definition(): array
    {
        return [
            'libelle' => $this->faker->unique()->company(),
        ];
    }
}

