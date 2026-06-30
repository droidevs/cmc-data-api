<?php

namespace Database\Factories;

use App\Models\Niveau;
use App\Models\Pole;
use App\Models\TypeFormation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Filiere factory based on real data from AvancementProgramme2025.xlsx.
 *
 * Real filieres observed (all TS Diplômante, Secteur: Digital et IA):
 *   DIA_DEV_TS     → Développement Digital
 *   DIA_DEVOAM_TS  → Développement Digital option Applications Mobiles
 *   DIA_DEVOWFS_TS → Développement Digital option Web Full Stack
 *   DIA_ID_TS      → Infrastructure Digitale
 *   DIA_IDOCS_TS   → Infrastructure Digitale option Cyber sécurité
 *   DIA_IDOSR_TS   → Infrastructure Digitale option Systèmes et Réseaux
 *   DIA_IDOCC_TS   → Infrastructure Digitale option Cloud Computing
 *
 * code_filiere format: {SECTEUR_ABBR}_{SPEC}_{NIVEAU}
 */
class FiliereFactory extends Factory
{
    /** @var array<string, array{libelle: string, secteur: string}> */
    private static array $realFilieres = [
        'DIA_DEV_TS'     => ['libelle' => 'Développement Digital',                                          'secteur' => 'Digital et Intelligence Artificielle'],
//        'DIA_DEVOAM_TS'  => ['libelle' => 'Développement Digital option Applications Mobiles',              'secteur' => 'Digital et Intelligence Artificielle'],
//        'DIA_DEVOWFS_TS' => ['libelle' => 'Développement Digital option Web Full Stack',                    'secteur' => 'Digital et Intelligence Artificielle'],
        'DIA_ID_TS'      => ['libelle' => 'Infrastructure Digitale',                                        'secteur' => 'Digital et Intelligence Artificielle'],
//        'DIA_IDOCS_TS'   => ['libelle' => 'Infrastructure Digitale option Cyber sécurité',                  'secteur' => 'Digital et Intelligence Artificielle'],
//        'DIA_IDOSR_TS'   => ['libelle' => 'Infrastructure Digitale option Systèmes et Réseaux',             'secteur' => 'Digital et Intelligence Artificielle'],
//        'DIA_IDOCC_TS'   => ['libelle' => 'Infrastructure Digitale option  Cloud Computing',                'secteur' => 'Digital et Intelligence Artificielle'],
    ];

    /** @var array<string, array{libelle: string, secteur: string}> Additional filieres for seeding variety */
    private static array $extraFilieres = [
        'GC_MCA_TS'      => ['libelle' => 'Management Commercial et Action de Vente',                       'secteur' => 'Commerce et Distribution'],
        'GC_CCA_TS'      => ['libelle' => 'Comptabilité et Contrôle de Gestion',                           'secteur' => 'Gestion et Commerce'],
        'BTP_TP_TS'      => ['libelle' => 'Travaux Publics',                                               'secteur' => 'Bâtiment et Travaux Publics'],
    ];

    public function definition(): array
    {
        $allFilieres = array_merge(self::$realFilieres, self::$extraFilieres);
        $code = $this->faker->randomElement(array_keys($allFilieres));
        $data = $allFilieres[$code];

        return [
            'code_filiere'      => $code,
            'pole_id'           => Pole::factory(),
            'niveau_id'         => Niveau::factory()->ts(),
            'type_formation_id' => TypeFormation::factory()->diplomante(),
            'libelle'           => $data['libelle'],
            'secteur'           => $data['secteur'],
        ];
    }

    /**
     * State: one of the 7 real DIA filieres from the Excel data.
     * Picks a random real one; call with ->forCode('DIA_DEV_TS') for a specific one.
     */
    public function real(): static
    {
        $code = $this->faker->randomElement(array_keys(self::$realFilieres));
        $data = self::$realFilieres[$code];

        return $this->state([
            'code_filiere' => $code,
            'libelle'      => $data['libelle'],
            'secteur'      => $data['secteur'],
        ]);
    }

    /** State: Développement Digital (most common in real data). */
    public function dev(): static
    {
        return $this->state([
            'code_filiere' => 'DIA_DEV_TS',
            'libelle'      => 'Développement Digital',
            'secteur'      => 'Digital et Intelligence Artificielle',
        ]);
    }

    /** State: Infrastructure Digitale. */
    public function id(): static
    {
        return $this->state([
            'code_filiere' => 'DIA_ID_TS',
            'libelle'      => 'Infrastructure Digitale',
            'secteur'      => 'Digital et Intelligence Artificielle',
        ]);
    }
}
