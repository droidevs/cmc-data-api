<?php

namespace Database\Factories;

use App\Models\Affectation;
use App\Models\Seance;
use App\Models\TimeRange;
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
            'date' => $this->faker->dateTimeBetween('-2 months', '+2 months')->format('Y-m-d'),
            'time_range_id' => TimeRange::factory(),
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

