<?php

namespace Database\Factories;

use App\Models\DateSeance;
use App\Models\Seance;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DateSeance> */
class DateSeanceFactory extends Factory
{
    protected $model = DateSeance::class;

    public function definition(): array
    {
        return [
            'seance_id' => Seance::factory(),
            'date' => $this->faker->dateTimeBetween('-2 months', '+2 months'),
        ];
    }
}

