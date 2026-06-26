<?php

namespace Database\Seeders;

use App\Models\Annee;
use App\Models\Espace;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\Pole;
use App\Models\TypeFormation;
use Illuminate\Database\Seeder;

/**
 * Seeds the full academic structure from real CMC data.
 *
 * Source: AvancementProgramme2025_NC10PDAI_03_04_2026_21_07_12.xlsx
 *
 * Insertion order respects FK constraints:
 *   Pole → Filiere → Annee → Groupe
 *                          → Module
 *
 * Every record is created with firstOrCreate so the seeder is idempotent
 * and can be re-run safely.
 */
class AcademicStructureSeeder extends Seeder
{
    // ── Real data extracted from AvancementProgramme.xlsx ─────────────────────

    /**
     * Filieres belonging to Pôle Digital et Intelligence Artificielle.
     * Format: code_filiere => [libelle, niveau_libelle, type_libelle, secteur]
     */
    private const FILIERES_DIGITAL = [
        'DIA_DEV_TS' => [
            'libelle'  => 'Développement Digital',
            'niveau'   => 'TS',
            'type'     => 'Diplômante',
            'secteur'  => 'Digital et Intelligence Artificielle',
        ],
        'DIA_ID_TS' => [
            'libelle'  => 'Infrastructure Digitale',
            'niveau'   => 'TS',
            'type'     => 'Diplômante',
            'secteur'  => 'Digital et Intelligence Artificielle',
        ],
        'DIA_DEVOAM_TS' => [
            'libelle'  => 'Développement Digital option Applications Mobiles',
            'niveau'   => 'TS',
            'type'     => 'Diplômante',
            'secteur'  => 'Digital et Intelligence Artificielle',
        ],
        'DIA_DEVOWFS_TS' => [
            'libelle'  => 'Développement Digital option Web Full Stack',
            'niveau'   => 'TS',
            'type'     => 'Diplômante',
            'secteur'  => 'Digital et Intelligence Artificielle',
        ],
        'DIA_IDOCS_TS' => [
            'libelle'  => 'Infrastructure Digitale option Cyber sécurité',
            'niveau'   => 'TS',
            'type'     => 'Diplômante',
            'secteur'  => 'Digital et Intelligence Artificielle',
        ],
        'DIA_IDOSR_TS' => [
            'libelle'  => 'Infrastructure Digitale option Systèmes et Réseaux',
            'niveau'   => 'TS',
            'type'     => 'Diplômante',
            'secteur'  => 'Digital et Intelligence Artificielle',
        ],
        'DIA_IDOCC_TS' => [
            'libelle'  => 'Infrastructure Digitale option Cloud Computing',
            'niveau'   => 'TS',
            'type'     => 'Diplômante',
            'secteur'  => 'Digital et Intelligence Artificielle',
        ],
    ];

    /**
     * Groupes with their filiere and year.
     * Format: code => [filiere_code, annee (1 or 2), effectif, mode]
     * Source: AvancementProgramme "Groupe", "Code Filière", "Année de formation", "Effectif Groupe"
     */
    private const GROUPES = [
        // 1ère année — Développement Digital (tronc commun)
        'DEV101' => ['filiere' => 'DIA_DEV_TS', 'annee' => 1, 'effectif' => 19, 'mode' => 'Résidentiel'],
        'DEV102' => ['filiere' => 'DIA_DEV_TS', 'annee' => 1, 'effectif' => 19, 'mode' => 'Résidentiel'],
        'DEV103' => ['filiere' => 'DIA_DEV_TS', 'annee' => 1, 'effectif' => 19, 'mode' => 'Résidentiel'],
        'DEV104' => ['filiere' => 'DIA_DEV_TS', 'annee' => 1, 'effectif' => 20, 'mode' => 'Résidentiel'],
        // 1ère année — Infrastructure Digitale (tronc commun)
        'ID101'  => ['filiere' => 'DIA_ID_TS',  'annee' => 1, 'effectif' => 20, 'mode' => 'Résidentiel'],
        'ID102'  => ['filiere' => 'DIA_ID_TS',  'annee' => 1, 'effectif' => 20, 'mode' => 'Résidentiel'],
        'ID103'  => ['filiere' => 'DIA_ID_TS',  'annee' => 1, 'effectif' => 20, 'mode' => 'Résidentiel'],
        'ID104'  => ['filiere' => 'DIA_ID_TS',  'annee' => 1, 'effectif' => 19, 'mode' => 'Résidentiel'],
        // 2ème année — Options (specialized)
        'DEVOAM201'  => ['filiere' => 'DIA_DEVOAM_TS',  'annee' => 2, 'effectif' => 13, 'mode' => 'Résidentiel'],
        'DEVOWFS201' => ['filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'effectif' => 20, 'mode' => 'Résidentiel'],
        'DEVOWFS202' => ['filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'effectif' => 19, 'mode' => 'Résidentiel'],
        'DEVOWFS203' => ['filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'effectif' => 16, 'mode' => 'Résidentiel'],
        'IDOCS201'   => ['filiere' => 'DIA_IDOCS_TS',   'annee' => 2, 'effectif' => 19, 'mode' => 'Résidentiel'],
        'IDOSR201'   => ['filiere' => 'DIA_IDOSR_TS',   'annee' => 2, 'effectif' => 19, 'mode' => 'Résidentiel'],
        'IDOCC201'   => ['filiere' => 'DIA_IDOCC_TS',   'annee' => 2, 'effectif' => 17, 'mode' => 'Résidentiel'],
    ];

    /**
     * Modules keyed by code_module.
     * Format: code => [libelle, filiere_code, annee (1 or 2), regional]
     * Source: AvancementProgramme "Code Module", "Module", "Code Filière", "Régional"
     * Note: same code can appear in multiple filieres (e.g. EGTS202 = Français for all TS yr2).
     * We model each (code_module, annee_id) pair uniquely.
     */
    private const MODULES = [
        // ── 1ère année — DIA_DEV_TS ──────────────────────────────────────────
        ['code' => 'M101',    'libelle' => 'Métier et formation en développement digital',  'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'M102',    'libelle' => 'Conception de bases de données',                'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'M103',    'libelle' => 'Programmation procédurale',                     'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'M104',    'libelle' => 'Développement d\'interfaces graphiques',        'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'M105',    'libelle' => 'Programmation JavaScript',                      'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => true],
        ['code' => 'M106',    'libelle' => 'Développement Web',                             'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'M107',    'libelle' => 'Sites Web dynamiques',                          'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => true],
        ['code' => 'M108',    'libelle' => 'Sécurité des systèmes d\'information',          'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'EGTS101', 'libelle' => 'Arabe',                                         'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'EGTS102', 'libelle' => 'Français',                                      'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'EGTS103', 'libelle' => 'Anglais',                                       'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'EGTS104', 'libelle' => 'Culture entrepreneuriale',                      'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'EGTS105', 'libelle' => 'Compétences comportementales',                  'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],
        ['code' => 'EGTS108', 'libelle' => 'Entrepreneuriat-PIE 1',                         'filiere' => 'DIA_DEV_TS', 'annee' => 1, 'regional' => false],

        // ── 1ère année — DIA_ID_TS ────────────────────────────────────────────
        ['code' => 'M105',    'libelle' => 'Gestion de l\'infrastructure virtualisée',      'filiere' => 'DIA_ID_TS',  'annee' => 1, 'regional' => false],
        ['code' => 'EGTS102', 'libelle' => 'Français',                                      'filiere' => 'DIA_ID_TS',  'annee' => 1, 'regional' => false],
        ['code' => 'EGTS104', 'libelle' => 'Culture entrepreneuriale',                      'filiere' => 'DIA_ID_TS',  'annee' => 1, 'regional' => false],
        ['code' => 'EGTS105', 'libelle' => 'Compétences comportementales',                  'filiere' => 'DIA_ID_TS',  'annee' => 1, 'regional' => false],

        // ── 2ème année — DIA_DEVOAM_TS ────────────────────────────────────────
        ['code' => 'M205',    'libelle' => 'Initiation aux composants et modèle d\'une application Android', 'filiere' => 'DIA_DEVOAM_TS', 'annee' => 2, 'regional' => true],
        ['code' => 'EGTS204', 'libelle' => 'Culture entrepreneuriale',                      'filiere' => 'DIA_DEVOAM_TS', 'annee' => 2, 'regional' => false],

        // ── 2ème année — DIA_DEVOWFS_TS ───────────────────────────────────────
        ['code' => 'M201',    'libelle' => 'Préparation d\'un projet web',                  'filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'regional' => false],
        ['code' => 'M202',    'libelle' => 'Approche agile',                                'filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'regional' => true],
        ['code' => 'M203',    'libelle' => 'Gestion des données',                           'filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'regional' => false],
        ['code' => 'M206',    'libelle' => 'Création d\'une application Cloud native',      'filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'regional' => false],
        ['code' => 'EGTS202', 'libelle' => 'Français',                                      'filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'regional' => false],
        ['code' => 'EGTSA206','libelle' => 'Culture et techniques avancées du numérique',   'filiere' => 'DIA_DEVOWFS_TS', 'annee' => 2, 'regional' => false],

        // ── 2ème année — DIA_IDOCS_TS ─────────────────────────────────────────
        ['code' => 'EGTS204', 'libelle' => 'Culture entrepreneuriale',                      'filiere' => 'DIA_IDOCS_TS', 'annee' => 2, 'regional' => false],

        // ── 2ème année — DIA_IDOSR_TS ─────────────────────────────────────────
        ['code' => 'EGTS203', 'libelle' => 'Anglais technique',                             'filiere' => 'DIA_IDOSR_TS', 'annee' => 2, 'regional' => false],

        // ── 2ème année — DIA_IDOCC_TS ─────────────────────────────────────────
        ['code' => 'EGTS202', 'libelle' => 'Français',                                      'filiere' => 'DIA_IDOCC_TS', 'annee' => 2, 'regional' => false],
    ];

    /**
     * Espaces (rooms / labs) per pole.
     * A Seance (session) is booked into an Espace within its own Pole.
     * Online / synchronous sessions have no Espace.
     */
    private const ESPACES_DIGITAL = [
        ['libelle' => 'Salle A101',    'capacite' => 25],
        ['libelle' => 'Salle A102',    'capacite' => 25],
        ['libelle' => 'Salle A103',    'capacite' => 25],
        ['libelle' => 'Salle A104',    'capacite' => 25],
        ['libelle' => 'Lab Réseau 1',  'capacite' => 20],
        ['libelle' => 'Lab Réseau 2',  'capacite' => 20],
        ['libelle' => 'Lab Dev 1',     'capacite' => 22],
        ['libelle' => 'Lab Dev 2',     'capacite' => 22],
        ['libelle' => 'Amphithéâtre',  'capacite' => 120],
    ];

    // ──────────────────────────────────────────────────────────────────────────

    public function run(): void
    {
        // All data in this file belongs to the "Digital et IA" pole.
        $poleDigital = Pole::where('libelle', 'Pôle Digital et Intelligence Artificielle')->firstOrFail();

        // ── 1. Espaces for Digital pole ────────────────────────────────────────
        foreach (self::ESPACES_DIGITAL as $espaceData) {
            \App\Models\Espace::firstOrCreate(
                ['libelle' => $espaceData['libelle'], 'pole_id' => $poleDigital->id],
                ['capacite' => $espaceData['capacite']]
            );
        }

        // ── 2. Filieres ────────────────────────────────────────────────────────
        foreach (self::FILIERES_DIGITAL as $code => $data) {
            $niveau = Niveau::where('libelle', $data['niveau'])->firstOrFail();
            $type   = TypeFormation::where('libelle', $data['type'])->firstOrFail();

            Filiere::firstOrCreate(
                ['code_filiere' => $code],
                [
                    'pole_id'           => $poleDigital->id,
                    'niveau_id'         => $niveau->id,
                    'type_formation_id' => $type->id,
                    'libelle'           => $data['libelle'],
                    'secteur'           => $data['secteur'],
                ]
            );
        }

        // ── 3. Annees (one per filiere × year) ────────────────────────────────
        // Build a lookup: filiere_code + annee_number → annee model id
        $anneeMap = [];  // "DIA_DEV_TS:1" => Annee model

        $requiredAnnees = collect(self::GROUPES)
            ->map(fn ($g) => ['filiere' => $g['filiere'], 'annee' => $g['annee']])
            ->merge(
                collect(self::MODULES)
                    ->map(fn ($m) => ['filiere' => $m['filiere'], 'annee' => $m['annee']])
            )
            ->unique(fn ($item) => $item['filiere'] . ':' . $item['annee']);

        foreach ($requiredAnnees as $item) {
            $key = $item['filiere'] . ':' . $item['annee'];
            if (isset($anneeMap[$key])) {
                continue;
            }

            $annee = Annee::firstOrCreate(
                ['filiere_code' => $item['filiere'], 'libelle' => $item['annee']]
            );
            $anneeMap[$key] = $annee;
        }

        // ── 4. Modules (keyed by code + annee) ────────────────────────────────
        foreach (self::MODULES as $mod) {
            $key   = $mod['filiere'] . ':' . $mod['annee'];
            $annee = $anneeMap[$key] ?? null;
            if (! $annee) {
                continue;
            }

            Module::firstOrCreate(
                ['code_module' => $mod['code'], 'annee_id' => $annee->id],
                [
                    'libelle'  => $mod['libelle'],
                    'regional' => $mod['regional'],
                ]
            );
        }

        // ── 5. Groupes ─────────────────────────────────────────────────────────
        foreach (self::GROUPES as $code => $data) {
            $key   = $data['filiere'] . ':' . $data['annee'];
            $annee = $anneeMap[$key] ?? null;
            if (! $annee) {
                continue;
            }

            Groupe::firstOrCreate(
                ['code' => $code],
                [
                    'annee_id' => $annee->id,
                    'effectif' => $data['effectif'],
                    'mode'     => $data['mode'],
                ]
            );
        }
    }
}
