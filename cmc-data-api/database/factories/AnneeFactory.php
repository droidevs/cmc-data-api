<?php

namespace Database\Factories;

use App\Models\Annee;
use App\Models\Filiere;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Annee> */
class AnneeFactory extends Factory
{
    protected $model = Annee::class;

    public function definition(): array
    {
        $startYear = (int) now()->subYears($this->faker->numberBetween(0, 2))->format('Y');
        $endYear = $startYear + 1;

        return [
            'filiere_code' => Filiere::factory(),
            'libelle' => $this->faker->randomElement([
                $startYear . '/' . $endYear,
                'Year 1',
                'Year 2',
            ]),
        ];
    }
}

