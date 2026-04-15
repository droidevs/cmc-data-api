<?php

namespace Database\Factories;

use App\Models\Niveau;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Niveau> */
class NiveauFactory extends Factory
{
    protected $model = Niveau::class;

    public function definition(): array
    {
        return [
            'libelle' => $this->faker->unique()->randomElement([
                '1st Year',
                '2nd Year',
                '3rd Year',
                'Specialization',
            ]),
        ];
    }
}

