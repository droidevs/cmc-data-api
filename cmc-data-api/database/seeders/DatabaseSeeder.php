<?php

namespace Database\Seeders;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Master seeder. Order matters and follows the dependency graph of the
 * schema — each seeder only ever references rows created by an earlier one:
 *
 *   1. ReferenceSeeder           — Poles, Niveaux, TypeFormations, TimeRanges
 *                                   (no FKs; everything else points at these)
 *   2. AcademicStructureSeeder   — Filieres -> Annees -> Groupes / Modules
 *                                   (needs Poles/Niveaux/TypeFormations)
 *   3. UsersSeeder               — app/staff accounts (independent of the
 *                                   academic graph, but kept after reference
 *                                   data so it never runs against an empty DB
 *                                   during local smoke-testing)
 *   4. FormateursSeeder          — real trainers (needs Poles)
 *   5. StagiairesSeeder          — real trainees (needs Groupes)
 *   6. PlanningSeeder            — Affectations + Seances (needs Groupes,
 *                                   Modules, Formateurs, TimeRanges — i.e.
 *                                   everything above)
 *   7. EvaluationSeeder          — Notes (needs Seances + Stagiaires, i.e.
 *                                   the output of steps 5 and 6)
 *
 * The previous version skipped FormateursSeeder/StagiairesSeeder entirely,
 * which meant PlanningSeeder (and therefore EvaluationSeeder) always ran
 * against fully-fabricated Formateur/Stagiaire factory records instead of
 * the real, curated ones those two seeders exist to load — defeating the
 * point of having real-data seeders at all. That's fixed here: real people
 * are seeded before anything that references them.
 */
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
            FormateursSeeder::class,
            StagiairesSeeder::class,
            PlanningSeeder::class,
            EvaluationSeeder::class,
        ]);
    }
}
