<?php

namespace Database\Factories;

use App\Models\Filiere;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Annee factory.
 *
 * From AvancementProgramme and lister_minimized:
 *   - "Année de formation" is always 1 or 2
 *   - lister_minimized maps these to "1ère année" / "2ème année"
 *
 * Groups like DEV101/DEV102/DEV103/DEV104 are all 1ère année of DIA_DEV_TS.
 * Groups like DEVOWFS201/202/203 are 2ème année of DIA_DEVOWFS_TS.
 */
class AnneeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'filiere_code' => Filiere::factory(),
            'libelle'      => $this->faker->randomElement([1, 2]),
        ];
    }

    /** State: 1ère année. */
    public function premiereAnnee(): static
    {
        return $this->state(['libelle' => 1]);
    }

    /** State: 2ème année. */
    public function deuxiemeAnnee(): static
    {
        return $this->state(['libelle' => 2]);
    }
}
