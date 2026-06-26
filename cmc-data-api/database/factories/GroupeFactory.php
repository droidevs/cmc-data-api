<?php

namespace Database\Factories;

use App\Models\Annee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Groupe factory based on real data from AvancementProgramme2025.xlsx.
 *
 * Real groupe codes follow the pattern:
 *   {FILIERE_ABBR}{ANNEE}{SEQ}
 *   e.g. DEV101, DEV102, DEV103, DEV104   → 1ère année DIA_DEV_TS
 *        ID101, ID102, ID103, ID104        → 1ère année DIA_ID_TS
 *        DEVOWFS201, DEVOWFS202, DEVOWFS203 → 2ème année DIA_DEVOWFS_TS
 *        DEVOAM201                          → 2ème année DIA_DEVOAM_TS
 *        IDOCS201, IDOSR201, IDOCC201       → 2ème année (ID options)
 *
 * Effectif (group size): 13–20 students observed in real data.
 * Mode: always "Résidentiel" in the real dataset.
 */
class GroupeFactory extends Factory
{
    /**
     * Real groupe codes with their effectifs.
     * @var array<string, int>
     */
    private static array $realGroupes = [
        'DEV101'     => 19,
        'DEV102'     => 19,
        'DEV103'     => 19,
        'DEV104'     => 20,
        'ID101'      => 20,
        'ID102'      => 20,
        'ID103'      => 20,
        'ID104'      => 19,
        'DEVOAM201'  => 13,
        'DEVOWFS201' => 20,
        'DEVOWFS202' => 19,
        'DEVOWFS203' => 16,
        'IDOCS201'   => 19,
        'IDOSR201'   => 19,
        'IDOCC201'   => 17,
    ];

    public function definition(): array
    {
        // Generate a plausible group code: random filiere abbr + year + seq
        $prefixes  = ['DEV', 'ID', 'DEVOAM', 'DEVOWFS', 'IDOCS', 'IDOSR', 'IDOCC', 'GC', 'BTP'];
        $annee     = $this->faker->randomElement([1, 2]);
        $seq       = $this->faker->numberBetween(1, 4);
        $prefix    = $this->faker->randomElement($prefixes);
        $code      = strtoupper($prefix) . $annee . '0' . $seq;

        return [
            'annee_id' => Annee::factory(),
            'code'     => $code,
            'effectif' => $this->faker->numberBetween(13, 25),
            'mode'     => 'Résidentiel',   // only value in real data
        ];
    }

    /**
     * State: one of the 15 real groupe codes from the Excel data.
     * Picks a random one — effectif matches real data.
     */
    public function real(): static
    {
        $code     = $this->faker->randomElement(array_keys(self::$realGroupes));
        $effectif = self::$realGroupes[$code];

        return $this->state([
            'code'     => $code,
            'effectif' => $effectif,
            'mode'     => 'Résidentiel',
        ]);
    }

    /** State: 1ère année group (DEV1xx / ID1xx pattern). */
    public function premiereAnnee(): static
    {
        $firstYear = ['DEV101', 'DEV102', 'DEV103', 'DEV104', 'ID101', 'ID102', 'ID103', 'ID104'];
        $code      = $this->faker->randomElement($firstYear);

        return $this->state([
            'code'     => $code,
            'effectif' => self::$realGroupes[$code],
        ]);
    }

    /** State: 2ème année group (xxx201 pattern). */
    public function deuxiemeAnnee(): static
    {
        $secondYear = ['DEVOAM201', 'DEVOWFS201', 'DEVOWFS202', 'DEVOWFS203', 'IDOCS201', 'IDOSR201', 'IDOCC201'];
        $code       = $this->faker->randomElement($secondYear);

        return $this->state([
            'code'     => $code,
            'effectif' => self::$realGroupes[$code],
        ]);
    }

    /** State: alternance mode (not in current Excel but valid for the model). */
    public function alternance(): static
    {
        return $this->state(['mode' => 'Alternance']);
    }
}
