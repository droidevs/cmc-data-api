<?php

namespace Database\Factories;

use App\Models\TimeRange;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TimeRange> */
class TimeRangeFactory extends Factory
{
    protected $model = TimeRange::class;

    public function definition(): array
    {
        // Common OFP / training slots
        $slots = [
            ['08:30', '11:00'],
            ['11:00', '13:30'],
            ['14:30', '17:00'],
            ['17:00', '19:00'],
        ];

        [$start, $end] = $this->faker->randomElement($slots);

        return [
            'start_time' => $start,
            'end_time' => $end,
        ];
    }
}

