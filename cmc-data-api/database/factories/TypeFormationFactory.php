<?php

namespace Database\Factories;

use App\Models\TypeFormation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TypeFormation> */
class TypeFormationFactory extends Factory
{
    protected $model = TypeFormation::class;

    public function definition(): array
    {
        return [
            'libelle' => $this->faker->unique()->randomElement([
                'Initial',
                'Continuing',
                'Apprenticeship',
                'Evening',
            ]),
        ];
    }
}

