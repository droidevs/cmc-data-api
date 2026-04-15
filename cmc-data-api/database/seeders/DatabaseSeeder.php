<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Optional reproducibility. Change/remove if you want full randomness.
        FakerFactory::create()->seed(20260415);

        $this->call([
            ReferenceSeeder::class,
            AcademicStructureSeeder::class,
            UsersSeeder::class,
            PlanningSeeder::class,
            EvaluationSeeder::class,
        ]);
    }
}
