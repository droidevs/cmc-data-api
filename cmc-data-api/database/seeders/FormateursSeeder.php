<?php

namespace Database\Seeders;

use App\Models\Formateur;
use App\Models\Pole;
use Illuminate\Database\Seeder;

/**
 * Seeds all 32 real formateurs from Base_Formateurs.xlsx.
 *
 * Key design decisions:
 *   - pole_id is resolved by an EXACT match against Pole.libelle (the
 *     'pole' / 'efp_mutualise' keys below are written to match
 *     ReferenceSeeder's libelles verbatim). The previous version used
 *     fuzzy str_contains() matching in both directions plus a suffix
 *     heuristic, which could silently resolve a formateur to the wrong
 *     pole — a real risk for a foreign key.
 *   - Attribute defaults (anything the spreadsheet doesn't dictate) come
 *     from FormateurFactory's ofppt()/vacataire() states, then the real,
 *     known values from self::FORMATEURS are overlaid on top. Real data
 *     always wins; the factory only fills gaps and keeps the shape
 *     consistent with every other Formateur created elsewhere in the app.
 *   - All real-data trainers have statut = 'OFPPT' except the two
 *     Vacataire fallbacks (H418299, I661572) found only in
 *     AvancementProgramme but absent from Base_Formateurs.
 */
class FormateursSeeder extends Seeder
{
    /**
     * Real formateurs from Base_Formateurs.xlsx.
     *
     * Format: mle => [nom_prenom, statut, affectation_pole, efp_mutualise_pole, mutualise, mhs, email_edu]
     *
     * pole / efp_mutualise: must match a Pole.libelle exactly (see ReferenceSeeder).
     */
    private const FORMATEURS = [
        // ── Mutualised from Pôle Gestion et Commerce → Pôle Digital ─────────
        '16984' => [
            'nom_prenom'    => 'MANIALI FATIMA-ZAHRA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'FATIMAZAHRA.MANIALI@ofppt-edu.ma',
        ],
        '18333' => [
            'nom_prenom'    => 'EL ARBAOUI ZOHAIR',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'ZOUHAIRE.ELARBAOUI@ofppt-edu.ma',
        ],
        '19207' => [
            'nom_prenom'    => 'KOURA ABDELGHANI',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'ABDELGHANI.KOURA@ofppt-edu.ma',
        ],
        '19309' => [
            'nom_prenom'    => 'RHIZLANE SAMIRA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'SAMIRA.RHIZLANE@ofppt-edu.ma',
        ],
        '19368' => [
            'nom_prenom'    => 'DKHISSI ANWAR',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'ANWAR.DKHISSI@ofppt-edu.ma',
        ],
        '19742' => [
            'nom_prenom'    => 'JABRI NEZHA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'NEZHA.JABRI@ofppt-edu.ma',
        ],
        '19829' => [
            'nom_prenom'    => 'THABIT MOHAMED',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'MOHAMED.THABIT@ofppt-edu.ma',
        ],
        '19836' => [
            'nom_prenom'    => 'BEN BRAHIM MOHAMMED',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'MOHAMMED.BENBRAHIM@ofppt-edu.ma',
        ],
        '19842' => [
            'nom_prenom'    => 'TILAOUI NABIL',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'NABIL.TILAOUI@ofppt-edu.ma',
        ],
        '19847' => [
            'nom_prenom'    => 'AMAOUI MORAD',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'MORAD.AMAOUI@ofppt-edu.ma',
        ],
        '19966' => [
            'nom_prenom'    => 'BELMIHI MOHAMED',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'MOHAMED.BELMIHI@ofppt-edu.ma',
        ],
        '20008' => [
            'nom_prenom'    => 'EL OUROUBA CHAIMAA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'CHAIMAA.ELOUROUBA@ofppt-edu.ma',
        ],
        '20022' => [
            'nom_prenom'    => 'EL AFOUI ZAHIRA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'ZAHIRA.ELAFOUI@ofppt-edu.ma',
        ],
        '20783' => [
            'nom_prenom'    => 'EL MESKINI HALIMA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'HALIMA.ELMESKINI@ofppt-edu.ma',
        ],
        '21089' => [
            'nom_prenom'    => 'SEDRAOUI HASSNA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'HASSNA.SEDRAOUI@ofppt-edu.ma',
        ],
        '21669' => [
            'nom_prenom'    => 'HAFIDI HASNA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'HASNA.HAFIDI@ofppt-edu.ma',
        ],
        '21693' => [
            'nom_prenom'    => 'HASNI AZZEDINE',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'AZZEDINE.HASNI@ofppt-edu.ma',
        ],
        '21705' => [
            'nom_prenom'    => 'SADKI AMAL',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'AMAL.SADKI@ofppt-edu.ma',
        ],
        '21794' => [
            'nom_prenom'    => 'TIJANI KHADIJA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'KHADIJA.TIJANI@ofppt-edu.ma',
        ],
        '21816' => [
            'nom_prenom'    => 'EL HADDAOUI FATIMA ZAHRA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Gestion et Commerce',
            'efp_mutualise' => 'Pôle Digital et Intelligence Artificielle',
            'mutualise'     => true,
            'mhs'           => 26,
            'email_edu'     => 'FATIMAZAHRA.ELHADDAOUI@ofppt-edu.ma',
        ],

        // ── Titulaires Pôle Digital et IA ─────────────────────────────────────
        '19307' => [
            'nom_prenom'    => 'AAMOU SALIM',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'SALIM.AAMOU@ofppt-edu.ma',
        ],
        '19522' => [
            'nom_prenom'    => 'EL AROUSSI HICHAM',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'HICHAM.ELAROUSSI@ofppt-edu.ma',
        ],
        '19578' => [
            'nom_prenom'    => 'SAOUDI ZAKARIAE',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'ZAKARIAE.SAOUDI@ofppt-edu.ma',
        ],
        '19642' => [
            'nom_prenom'    => 'YOUSSFI YASSIR',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'YASSIR.YOUSSFI@ofppt-edu.ma',
        ],
        '19864' => [
            'nom_prenom'    => 'LACHHEB FAISSAL',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'FAISSAL.LACHHEB@ofppt-edu.ma',
        ],
        '20286' => [
            'nom_prenom'    => 'EL JAAFARI IMAD',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'IMAD.ELJAAFARI@ofppt-edu.ma',
        ],
        '20638' => [
            'nom_prenom'    => 'REDOUANE KAIDA',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'REDOUANE.KAIDA@ofppt-edu.ma',
        ],
        '20710' => [
            'nom_prenom'    => 'BOULMANI HICHAM',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'HICHAM.BOULMANI@ofppt-edu.ma',
        ],
        '20732' => [
            'nom_prenom'    => 'RABOUZE MOUHSSINE',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'MOUHSSINE.RABOUZE@ofppt-edu.ma',
        ],
        '20733' => [
            'nom_prenom'    => 'JAOUHAR BILAL',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'BILAL.JAOUHAR@ofppt-edu.ma',
        ],
        '20903' => [
            'nom_prenom'    => 'HANANE MOHAMED',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'MOHAMED.HANANE@ofppt-edu.ma',
        ],
        '20983' => [
            'nom_prenom'    => 'DIOURI YASSINE',
            'statut'        => 'OFPPT',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 26,
            'email_edu'     => 'YASSINE.DIOURI@ofppt-edu.ma',
        ],

        // ── Vacataires found in AvancementProgramme but not in Base_Formateurs ─
        'H418299' => [
            'nom_prenom'    => 'VACATAIRE H418299',
            'statut'        => 'Vacataire',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 0,
            'email_edu'     => null,
        ],
        'I661572' => [
            'nom_prenom'    => 'VACATAIRE I661572',
            'statut'        => 'Vacataire',
            'pole'          => 'Pôle Digital et Intelligence Artificielle',
            'efp_mutualise' => null,
            'mutualise'     => false,
            'mhs'           => 0,
            'email_edu'     => null,
        ],
    ];

    public function run(): void
    {
        // Pre-load poles for exact lookup (must match ReferenceSeeder's libelles).
        $poles = Pole::all()->keyBy(fn ($p) => $p->libelle);

        $inserted = 0;
        $skipped  = 0;

        foreach (self::FORMATEURS as $mle => $data) {
            $pole = $poles->get($data['pole']);

            if (! $pole) {
                $this->command?->warn("Pole not found for formateur {$mle}: {$data['pole']} (run ReferenceSeeder first)");
                $skipped++;
                continue;
            }

            $efpMutualiseId = null;
            if ($data['efp_mutualise']) {
                $efpPole = $poles->get($data['efp_mutualise']);
                if (! $efpPole) {
                    $this->command?->warn("EFP mutualisé pole not found for formateur {$mle}: {$data['efp_mutualise']}");
                } else {
                    $efpMutualiseId = $efpPole->id;
                }
            }

            // Base shape/defaults from the factory's matching state, then overlay
            // the real known values — real data always wins.
            $base = $data['statut'] === 'OFPPT'
                ? \App\Models\Formateur::factory()->ofppt()->makeOne()->toArray()
                : \App\Models\Formateur::factory()->vacataire()->makeOne()->toArray();

            unset($base['mle']); // PK is the array key, not factory-generated

            $attributes = array_merge($base, [
                'pole_id'       => $pole->id,
                'nom_prenom'    => $data['nom_prenom'],
                'statut'        => $data['statut'],
                'email_edu'     => $data['email_edu'],
                'mhs'           => $data['mhs'],
                'efp_mutualise' => $efpMutualiseId,
                'mutualise'     => $data['mutualise'],
            ]);

            Formateur::firstOrCreate(['mle' => (string) $mle], $attributes);

            $inserted++;
        }

        $this->command?->info("FormateursSeeder: {$inserted} inserted, {$skipped} skipped.");
    }
}
