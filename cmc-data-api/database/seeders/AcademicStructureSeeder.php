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
 *   Pole → Filiere → Annee → Module
 *                          → Groupe
 *
 * Domain model:
 *   - A Filiere represents ONLY the general training program
 *     (e.g. "Développement Digital"), never a specialization/option.
 *   - An Annee belongs to a Filiere and carries a human-readable label
 *     that encodes both the year and, when applicable, the option
 *     (e.g. "2ème année - Option Développement Web Full Stack").
 *   - Groupes and Modules belong to an Annee, never directly to a Filiere.
 *
 * Every record is created with firstOrCreate so the seeder is idempotent
 * and can be re-run safely.
 */
class AcademicStructureSeeder extends Seeder
{
    // ── Real data extracted from AvancementProgramme.xlsx ─────────────────────

    /**
     * General filieres belonging to Pôle Digital et Intelligence Artificielle.
     * Options/specializations are NOT filieres — they live as Annee labels.
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
    ];

    /**
     * Every Annee that exists for each general filiere, in order.
     * The label encodes the year and, for 2nd-year groups, the option.
     * This is the single source of truth for Annee creation — Modules
     * and Groupes below must reference one of these labels exactly.
     *
     * Format: filiere_code => [label, label, ...]
     */
    private const ANNEES = [
        'DIA_DEV_TS' => [
            '1ère année - Tronc Commun',
            '2ème année - Option Développement Web Full Stack',
            '2ème année - Option Applications Mobiles',
        ],
        'DIA_ID_TS' => [
            '1ère année - Tronc Commun',
            '2ème année - Option Cyber sécurité',
            '2ème année - Option Systèmes et Réseaux',
            '2ème année - Option Cloud Computing',
        ],
    ];

    /**
     * Groupes with their general filiere and annee label.
     * Format: code => [filiere_code, annee_label, effectif, mode]
     * Source: AvancementProgramme "Groupe", "Code Filière", "Année de formation", "Effectif Groupe"
     */
    private const GROUPES = [
        // 1ère année — Développement Digital (tronc commun)
        'DEV101' => ['filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'effectif' => 19, 'mode' => 'Résidentiel'],
        'DEV102' => ['filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'effectif' => 19, 'mode' => 'Résidentiel'],
        'DEV103' => ['filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'effectif' => 19, 'mode' => 'Résidentiel'],
        'DEV104' => ['filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'effectif' => 20, 'mode' => 'Résidentiel'],
        // 1ère année — Infrastructure Digitale (tronc commun)
        'ID101'  => ['filiere' => 'DIA_ID_TS',  'annee' => '1ère année - Tronc Commun', 'effectif' => 20, 'mode' => 'Résidentiel'],
        'ID102'  => ['filiere' => 'DIA_ID_TS',  'annee' => '1ère année - Tronc Commun', 'effectif' => 20, 'mode' => 'Résidentiel'],
        'ID103'  => ['filiere' => 'DIA_ID_TS',  'annee' => '1ère année - Tronc Commun', 'effectif' => 20, 'mode' => 'Résidentiel'],
        'ID104'  => ['filiere' => 'DIA_ID_TS',  'annee' => '1ère année - Tronc Commun', 'effectif' => 19, 'mode' => 'Résidentiel'],
        // 2ème année — Options (specialized)
        'DEVOAM201'  => ['filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Applications Mobiles',              'effectif' => 13, 'mode' => 'Résidentiel'],
        'DEVOWFS201' => ['filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack',      'effectif' => 20, 'mode' => 'Résidentiel'],
        'DEVOWFS202' => ['filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack',      'effectif' => 19, 'mode' => 'Résidentiel'],
        'DEVOWFS203' => ['filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack',      'effectif' => 16, 'mode' => 'Résidentiel'],
        'IDOCS201'   => ['filiere' => 'DIA_ID_TS',  'annee' => '2ème année - Option Cyber sécurité',                    'effectif' => 19, 'mode' => 'Résidentiel'],
        'IDOSR201'   => ['filiere' => 'DIA_ID_TS',  'annee' => '2ème année - Option Systèmes et Réseaux',               'effectif' => 19, 'mode' => 'Résidentiel'],
        'IDOCC201'   => ['filiere' => 'DIA_ID_TS',  'annee' => '2ème année - Option Cloud Computing',                   'effectif' => 17, 'mode' => 'Résidentiel'],
    ];

    /**
     * Modules keyed by code_module.
     * Format: [code, libelle, filiere_code, annee_label, regional]
     * Source: AvancementProgramme "Code Module", "Module", "Code Filière", "Régional"
     * Note: same code can appear under multiple annees (e.g. EGTS202 = Français
     * for several 2nd-year options). We model each (code_module, annee_id)
     * pair uniquely.
     */
    private const MODULES = [
        // ── 1ère année - Tronc Commun — DIA_DEV_TS ───────────────────────────
        ['code' => 'M101',    'libelle' => 'Métier et formation en développement digital',  'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'M102',    'libelle' => 'Conception de bases de données',                'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'M103',    'libelle' => 'Programmation procédurale',                     'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'M104',    'libelle' => 'Développement d\'interfaces graphiques',        'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'M105',    'libelle' => 'Programmation JavaScript',                      'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => true],
        ['code' => 'M106',    'libelle' => 'Développement Web',                             'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'M107',    'libelle' => 'Sites Web dynamiques',                          'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => true],
        ['code' => 'M108',    'libelle' => 'Sécurité des systèmes d\'information',          'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS101', 'libelle' => 'Arabe',                                         'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS102', 'libelle' => 'Français',                                      'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS103', 'libelle' => 'Anglais',                                       'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS104', 'libelle' => 'Culture entrepreneuriale',                      'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS105', 'libelle' => 'Compétences comportementales',                  'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS108', 'libelle' => 'Entrepreneuriat-PIE 1',                         'filiere' => 'DIA_DEV_TS', 'annee' => '1ère année - Tronc Commun', 'regional' => false],

        // ── 1ère année - Tronc Commun — DIA_ID_TS ────────────────────────────
        ['code' => 'M105',    'libelle' => 'Gestion de l\'infrastructure virtualisée',      'filiere' => 'DIA_ID_TS',  'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS102', 'libelle' => 'Français',                                      'filiere' => 'DIA_ID_TS',  'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS104', 'libelle' => 'Culture entrepreneuriale',                      'filiere' => 'DIA_ID_TS',  'annee' => '1ère année - Tronc Commun', 'regional' => false],
        ['code' => 'EGTS105', 'libelle' => 'Compétences comportementales',                  'filiere' => 'DIA_ID_TS',  'annee' => '1ère année - Tronc Commun', 'regional' => false],

        // ── 2ème année - Option Applications Mobiles — DIA_DEV_TS ────────────
        ['code' => 'M205',    'libelle' => 'Initiation aux composants et modèle d\'une application Android', 'filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Applications Mobiles', 'regional' => true],
        ['code' => 'EGTS204', 'libelle' => 'Culture entrepreneuriale',                      'filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Applications Mobiles', 'regional' => false],

        // ── 2ème année - Option Développement Web Full Stack — DIA_DEV_TS ────
        ['code' => 'M201',    'libelle' => 'Préparation d\'un projet web',                  'filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack', 'regional' => false],
        ['code' => 'M202',    'libelle' => 'Approche agile',                                'filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack', 'regional' => true],
        ['code' => 'M203',    'libelle' => 'Gestion des données',                           'filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack', 'regional' => false],
        ['code' => 'M206',    'libelle' => 'Création d\'une application Cloud native',      'filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack', 'regional' => false],
        ['code' => 'EGTS202', 'libelle' => 'Français',                                      'filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack', 'regional' => false],
        ['code' => 'EGTSA206','libelle' => 'Culture et techniques avancées du numérique',   'filiere' => 'DIA_DEV_TS', 'annee' => '2ème année - Option Développement Web Full Stack', 'regional' => false],

        // ── 2ème année - Option Cyber sécurité — DIA_ID_TS ───────────────────
        ['code' => 'EGTS204', 'libelle' => 'Culture entrepreneuriale',                      'filiere' => 'DIA_ID_TS', 'annee' => '2ème année - Option Cyber sécurité', 'regional' => false],

        // ── 2ème année - Option Systèmes et Réseaux — DIA_ID_TS ──────────────
        ['code' => 'EGTS203', 'libelle' => 'Anglais technique',                             'filiere' => 'DIA_ID_TS', 'annee' => '2ème année - Option Systèmes et Réseaux', 'regional' => false],

        // ── 2ème année - Option Cloud Computing — DIA_ID_TS ──────────────────
        ['code' => 'EGTS202', 'libelle' => 'Français',                                      'filiere' => 'DIA_ID_TS', 'annee' => '2ème année - Option Cloud Computing', 'regional' => false],
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

    /**
     * Lookup of "filiere_code:annee_label" → Annee model, populated by
     * createAnnees() and consumed by createModules() / createGroupes().
     *
     * @var array<string, Annee>
     */
    private array $anneeMap = [];

    public function run(): void
    {
        // All data in this file belongs to the "Digital et IA" pole.
        $poleDigital = Pole::where('libelle', 'Pôle Digital et Intelligence Artificielle')->firstOrFail();

        $this->createEspaces($poleDigital);
        $this->createFilieres($poleDigital);
        $this->createAnnees();
        $this->createModules();
        $this->createGroupes();
    }

    // ── 1. Espaces for Digital pole ─────────────────────────────────────────

    private function createEspaces(Pole $poleDigital): void
    {
        foreach (self::ESPACES_DIGITAL as $espaceData) {
            Espace::firstOrCreate(
                ['libelle' => $espaceData['libelle'], 'pole_id' => $poleDigital->id],
                ['capacite' => $espaceData['capacite']]
            );
        }
    }

    // ── 2. Filieres (general programs only) ─────────────────────────────────

    private function createFilieres(Pole $poleDigital): void
    {
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
    }

    // ── 3. Annees (every year/option per filiere, from ANNEES) ─────────────

    /**
     * Creates every Annee declared in self::ANNEES and fills $anneeMap.
     * This is the single source of truth for which Annees exist — Modules
     * and Groupes are not allowed to implicitly create new ones.
     */
    private function createAnnees(): void
    {
        foreach (self::ANNEES as $filiereCode => $labels) {
            foreach ($labels as $label) {
                $annee = Annee::firstOrCreate(
                    ['filiere_code' => $filiereCode, 'libelle' => $label]
                );

                $this->anneeMap($filiereCode, $label, $annee);
            }
        }
    }

    // ── 4. Modules (keyed by code + annee) ──────────────────────────────────

    private function createModules(): void
    {
        foreach (self::MODULES as $mod) {
            $annee = $this->resolveAnnee($mod['filiere'], $mod['annee'], 'Module', $mod['code']);
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
    }

    // ── 5. Groupes ────────────────────────────────────────────────────────

    private function createGroupes(): void
    {
        foreach (self::GROUPES as $code => $data) {
            $annee = $this->resolveAnnee($data['filiere'], $data['annee'], 'Groupe', $code);
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

    // ── Annee lookup helpers ─────────────────────────────────────────────

    private function anneeMap(string $filiereCode, string $label, Annee $annee): void
    {
        $this->anneeMap[$this->anneeKey($filiereCode, $label)] = $annee;
    }

    private function anneeKey(string $filiereCode, string $label): string
    {
        return $filiereCode . ':' . $label;
    }

    /**
     * Resolves an Annee model from the (filiere, label) pair, or logs and
     * returns null if it's missing from self::ANNEES — this signals a data
     * entry mistake in MODULES/GROUPES rather than silently failing.
     */
    private function resolveAnnee(string $filiereCode, string $label, string $context, string $itemCode): ?Annee
    {
        $annee = $this->anneeMap[$this->anneeKey($filiereCode, $label)] ?? null;

        if (! $annee) {
            $this->command?->warn(
                "Skipping {$context} [{$itemCode}]: no Annee \"{$label}\" declared for filiere \"{$filiereCode}\" in ANNEES."
            );
        }

        return $annee;
    }
}
