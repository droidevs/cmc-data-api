<?php

namespace Database\Factories;

use App\Models\TimeRange;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TimeRange> */
class TimeRangeFactory extends Factory
{
    protected $model = TimeRange::class;

    /**
     * Standard OFPPT / CMC daily time slots.
     * The seeder should use firstOrCreate for these so there are no duplicates.
     */
    public static array $officialSlots = [
        ['08:30', '11:00'],  // Morning slot 1
        ['11:00', '13:30'],  // Morning slot 2
        ['13:30', '16:00'],  // Afternoon slot 1
        ['16:00', '18:30'],  // Afternoon slot 2 (evening classes)
    ];

    public function definition(): array
    {
        [$start, $end] = $this->faker->randomElement(self::$officialSlots);

        return [
            'start_time' => $start,
            'end_time'   => $end,
        ];
    }

    public function morning(): static
    {
        return $this->state(fn () => [
            'start_time' => '08:30',
            'end_time'   => '11:00',
        ]);
    }

    public function afternoon(): static
    {
        return $this->state(fn () => [
            'start_time' => '13:30',
            'end_time'   => '16:00',
        ]);
    }
}
