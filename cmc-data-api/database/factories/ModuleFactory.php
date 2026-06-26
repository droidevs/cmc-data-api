<?php

namespace Database\Factories;

use App\Models\Annee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Module factory based on real data from AvancementProgramme2025.xlsx.
 *
 * Real code_module formats:
 *   M{ANNEE}{SEQ}   → specialist modules, e.g. M101, M205, M207
 *   EGTS{ANNEE}{SEQ}→ general education (transversaux), e.g. EGTS102, EGTS204
 *   EGTSA{ANNEE}{SEQ} → advanced transversaux, e.g. EGTSA206
 *
 * Régional: "O" (true) = shared across filieres, "N" (false) = filiere-specific.
 * In real data ~40% are regional.
 *
 * A selection of real modules per series is embedded for realistic output.
 */
class ModuleFactory extends Factory
{
    /**
     * Real modules from AvancementProgramme (code → [libelle, regional]).
     * @var array<string, array{libelle: string, regional: bool}>
     */
    private static array $realModules = [
        // Année 1 — general
        'EGTS101' => ['libelle' => 'Arabe',                                                          'regional' => false],
        'EGTS102' => ['libelle' => 'Français',                                                       'regional' => false],
        'EGTS103' => ['libelle' => 'Anglais technique',                                              'regional' => false],
        'EGTS104' => ['libelle' => 'Culture entrepreneuriale',                                       'regional' => false],
        'EGTS105' => ['libelle' => 'Compétences comportementales',                                   'regional' => false],
        'EGTS108' => ['libelle' => 'Entrepreneuriat-PIE 1',                                          'regional' => false],
        // Année 1 — DEV
        'M101'    => ['libelle' => 'Métier et formation en développement digital',                   'regional' => false],
        'M102'    => ['libelle' => 'Les bases de l\'algorithmique',                                  'regional' => false],
        'M103'    => ['libelle' => 'Programmation Orienté Objet',                                    'regional' => false],
        'M104'    => ['libelle' => 'Sites Web statiques',                                            'regional' => true],
        'M105'    => ['libelle' => 'Programmation JavaScript',                                       'regional' => true],
        'M106'    => ['libelle' => 'Manipulation des bases de données',                              'regional' => false],
        'M107'    => ['libelle' => 'Sites Web dynamiques',                                           'regional' => true],
        'M108'    => ['libelle' => 'Sécurité des systèmes d\'information',                           'regional' => false],
        // Année 2 — general
        'EGTS202' => ['libelle' => 'Français',                                                       'regional' => false],
        'EGTS203' => ['libelle' => 'Anglais technique',                                              'regional' => false],
        'EGTS204' => ['libelle' => 'Culture entrepreneuriale',                                       'regional' => false],
        'EGTS205' => ['libelle' => 'Compétences comportementales',                                   'regional' => false],
        'EGTSA206'=> ['libelle' => 'Culture et techniques avancées du numérique',                    'regional' => false],
        // Année 2 — DEV/DEVOWFS
        'M201'    => ['libelle' => 'Préparation d\'un projet web',                                   'regional' => false],
        'M202'    => ['libelle' => 'Approche agile',                                                 'regional' => true],
        'M203'    => ['libelle' => 'Gestion des données',                                            'regional' => false],
        'M204'    => ['libelle' => 'Développement front-end',                                        'regional' => true],
        'M205'    => ['libelle' => 'Développement back-end',                                         'regional' => true],
        'M206'    => ['libelle' => 'Création d\'une application Cloud native',                       'regional' => false],
        'M207'    => ['libelle' => 'Développement multiplateforme',                                  'regional' => true],
        // Année 2 — DEVOAM (Android)
        'M205_AM' => ['libelle' => 'Initiation aux composants et modèle d\'une application Android', 'regional' => true],
        // Année 2 — IDOCS (Cyber)
        'M201_CS' => ['libelle' => 'Fondamentaux de la cybersécurité',                               'regional' => false],
        'M203_CS' => ['libelle' => 'Analyse des attaques et des incidents de Cybersécurité',         'regional' => true],
        // Année 2 — IDOCC (Cloud)
        'M201_CC' => ['libelle' => 'Architecture Cloud',                                             'regional' => false],
        'M202_CC' => ['libelle' => 'Environnement Cloud propriétaire en ligne public',               'regional' => true],
    ];

    public function definition(): array
    {
        $code = $this->faker->randomElement(array_keys(self::$realModules));
        $data = self::$realModules[$code];

        return [
            'code_module' => $code,
            'annee_id'    => Annee::factory(),
            'libelle'     => $data['libelle'],
            'regional'    => $data['regional'],
        ];
    }

    /** State: a real module from the Excel data. */
    public function real(): static
    {
        $code = $this->faker->randomElement(array_keys(self::$realModules));
        $data = self::$realModules[$code];

        return $this->state([
            'code_module' => $code,
            'libelle'     => $data['libelle'],
            'regional'    => $data['regional'],
        ]);
    }

    /** State: a transversal / general education module (EGTS*). */
    public function transversal(): static
    {
        $transversaux = array_filter(
            self::$realModules,
            fn ($code) => str_starts_with($code, 'EGTS'),
            ARRAY_FILTER_USE_KEY
        );
        $code = $this->faker->randomElement(array_keys($transversaux));
        $data = $transversaux[$code];

        return $this->state([
            'code_module' => $code,
            'libelle'     => $data['libelle'],
            'regional'    => $data['regional'],
        ]);
    }

    /** State: a regional/shared module (Régional = true, "O" in Excel). */
    public function regional(): static
    {
        return $this->state(['regional' => true]);
    }
}
