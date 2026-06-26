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
            // Match StoreSeanceRequest validation: cours|cc|efm|exam
            'type'           => $this->faker->randomElement(['cours', 'cc', 'efm', 'exam']),
            'date'           => $this->faker->dateTimeBetween('-4 months', '+2 months')->format('Y-m-d'),
            'time_range_id'  => TimeRange::factory(),
            'espace_id'      => null, // nullable — seeder will assign when relevant
        ];
    }

    public function cours(): static
    {
        return $this->state(fn () => ['type' => 'cours']);
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

    /** Place this session in the current academic year window. */
    public function currentYear(): static
    {
        return $this->state(fn () => [
            'date' => $this->faker->dateTimeBetween('2025-09-01', '2026-06-30')->format('Y-m-d'),
        ]);
    }
}
