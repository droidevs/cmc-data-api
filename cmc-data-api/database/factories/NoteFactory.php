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
            'seance_id'     => Seance::factory(),
            'stagiaire_cef' => Stagiaire::factory(),
            'valeur'        => $valeur,
            'type'          => $this->faker->randomElement(['cc', 'efm', 'tp', 'th', 'syn', 'exam']),
            // Match StoreNoteRequest validation: Admis|Redoublant|Abandon|Rattrapage
            'decision'      => $valeur >= 10
                ? 'Admis'
                : $this->faker->randomElement(['Redoublant', 'Rattrapage']),
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

    public function admis(): static
    {
        return $this->state(fn () => [
            'valeur'   => $this->faker->randomFloat(2, 10, 20),
            'decision' => 'Admis',
        ]);
    }

    public function redoublant(): static
    {
        return $this->state(fn () => [
            'valeur'   => $this->faker->randomFloat(2, 0, 9.99),
            'decision' => 'Redoublant',
        ]);
    }

    public function abandon(): static
    {
        return $this->state(fn () => [
            'valeur'   => null,
            'decision' => 'Abandon',
        ]);
    }
}
