<?php

namespace Database\Factories;

use App\Models\Affectation;
use App\Models\Seance;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Seance> */
class SeanceFactory extends Factory
{
    protected $model = Seance::class;

    public function definition(): array
    {
        return [
            'affectation_id' => Affectation::factory(),
            'type' => $this->faker->randomElement(['cours', 'cc', 'efm', 'exam']),
        ];
    }

    public function cours(): static
    {
        return $this->state(fn () => ['type' => 'cours']);
    }

    public function efm(): static
    {
        return $this->state(fn () => ['type' => 'efm']);
    }
}

