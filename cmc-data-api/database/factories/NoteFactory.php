<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Seance;
use App\Models\Stagiaire;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Note> */
class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition(): array
    {
        $valeur = $this->faker->randomFloat(2, 0, 20);

        return [
            'seance_id' => Seance::factory(),
            'stagiaire_cef' => Stagiaire::factory(),
            'valeur' => $valeur,
            'type' => $this->faker->randomElement(['cc', 'efm', 'exam']),
            'decision' => $valeur >= 10 ? 'Validated' : $this->faker->randomElement(['Retake', 'Failed']),
        ];
    }

    public function cc(): static
    {
        return $this->state(fn () => ['type' => 'cc']);
    }

    public function efm(): static
    {
        return $this->state(fn () => ['type' => 'efm']);
    }

    public function exam(): static
    {
        return $this->state(fn () => ['type' => 'exam']);
    }
}

