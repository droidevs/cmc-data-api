<?php

namespace Database\Factories;

use App\Models\Groupe;
use App\Models\Stagiaire;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Stagiaire> */
class StagiaireFactory extends Factory
{
    protected $model = Stagiaire::class;

    public function definition(): array
    {
        $genre = $this->faker->randomElement(['M', 'F']);

        return [
            'cef' => strtoupper(Str::random(12)),
            'groupe_id' => Groupe::factory(),
            'cni' => strtoupper($this->faker->unique()->bothify('??######')),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName($genre === 'M' ? 'male' : 'female'),
            'date_naissance' => $this->faker->dateTimeBetween('-25 years', '-16 years')->format('Y-m-d'),
            'genre' => $genre,
        ];
    }
}

