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
        return [
            'filiere_code' => Filiere::factory(),
            // Deterministic default; the seeder should override using states/sequences.
            'libelle' => '1ère année - Tronc commun',
        ];
    }

    public function firstYearCommon(): static
    {
        return $this->state(fn () => ['libelle' => '1ère année - Tronc commun']);
    }

    public function optionSecondYear(string $option): static
    {
        return $this->state(fn () => ['libelle' => '2ème année - ' . $option]);
    }
}

