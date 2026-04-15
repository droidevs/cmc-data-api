<?php

namespace Database\Factories;

use App\Models\Formateur;
use App\Models\Pole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Formateur> */
class FormateurFactory extends Factory
{
    protected $model = Formateur::class;

    public function definition(): array
    {
        return [
            'mle' => strtoupper(Str::random(10)),
            'pole_id' => Pole::factory(),
            'nom_prenom' => $this->faker->name(),
            'statut' => $this->faker->randomElement(['Permanent', 'Vacataire', 'Contractor', null]),
            'email_edu' => $this->faker->unique()->safeEmail(),
            'mhs' => $this->faker->randomFloat(2, 0, 300),
        ];
    }
}

