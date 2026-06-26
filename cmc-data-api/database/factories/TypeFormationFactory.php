<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * TypeFormation represents the OFPPT programme type.
 * The only type observed in Excel data is "Diplômante".
 * Other valid OFPPT types are included for completeness.
 */
class TypeFormationFactory extends Factory
{
    public function definition(): array
    {
        static $types = [
            'Diplômante',    // Only type in real Excel data
            'Qualifiante',
            'Certificante',
        ];

        return [
            'libelle' => $this->faker->randomElement($types),
        ];
    }

    /** State: Diplômante (matches all real data from AvancementProgramme). */
    public function diplomante(): static
    {
        return $this->state(['libelle' => 'Diplômante']);
    }
}
