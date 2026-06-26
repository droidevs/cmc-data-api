<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Pole names are derived from the real CMC Béni Mellal structure
 * found in Base_Formateurs.xlsx "Affectation" column.
 *
 * Real poles observed:
 *   - Pôle Digital et IA (Pôle Digital et Intelligence Artificielle)
 *   - Pôle Gestion et Commerce
 * Additional plausible CMC poles are included for test coverage.
 */
class PoleFactory extends Factory
{
    public function definition(): array
    {
        static $poles = [
            'Pôle Digital et Intelligence Artificielle',
            'Pôle Gestion et Commerce',
            'Pôle Bâtiment et Travaux Publics',
            'Pôle Mécanique et Électromécanique',
            'Pôle Hôtellerie et Tourisme',
            'Pôle Agriculture et Agroalimentaire',
            'Pôle Transport et Logistique',
            'Pôle Arts et Artisanat',
        ];

        static $used = [];

        $remaining = array_diff($poles, $used);
        $libelle = $remaining
            ? $this->faker->randomElement(array_values($remaining))
            : 'Pôle ' . $this->faker->unique()->word();

        $used[] = $libelle;

        return [
            'libelle' => $libelle,
        ];
    }
}
