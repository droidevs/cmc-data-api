<?php

namespace Database\Factories;

use App\Models\Pole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Formateur factory based on real data from Base_Formateurs.xlsx.
 *
 * Real MLE range: 16984–21816 (numeric strings, all OFPPT in the real data).
 * Real MHS: always 26 in the dataset (standard OFPPT monthly quota).
 * Email pattern: PRENOM.NOM@ofppt-edu.ma (sometimes NOM1 suffix for duplicates).
 * Statut: all "OFPPT" in real data; "Vacataire"/"Contractuel" added for coverage.
 *
 * Real names (used as a pool for realistic output):
 *   MANIALI FATIMA-ZAHRA, EL ARBAOUI ZOHAIR, KOURA ABDELGHANI,
 *   AAMOU SALIM, SAOUDI ZAKARIAE, LACHHEB FAISSAL, YOUSSFI YASSIR,
 *   JABRI NEZHA, THABIT MOHAMED, TILAOUI NABIL, BOULMANI HICHAM,
 *   RABOUZE MOUHSSINE, DIOURI YASSINE, TIJANI KHADIJA, SADKI AMAL…
 */
class FormateurFactory extends Factory
{
    /**
     * Moroccan family names pool (matching real data style: uppercase).
     * @var list<string>
     */
    private static array $noms = [
        'AAMOU', 'AMAOUI', 'AMJANE', 'ARSALAN', 'AZOUGAGHE',
        'BELMIHI', 'BEN BRAHIM', 'BEN YOUSSEF', 'BOULMANI', 'BOUGHANIM',
        'DAHAOUI', 'DIOURI', 'DKHISSI',
        'EL AFOUI', 'EL AROUSSI', 'EL GHABI', 'EL JAAFARI', 'EL MESKINI',
        'EL OUROUBA', 'EL ARBAOUI',
        'FADILI', 'FATTOUR',
        'HAFIDI', 'HAMMOUNI', 'HANANE', 'HASNI',
        'JABRI', 'JAOUHAR',
        'KADAR', 'KOURA',
        'LACHHEB',
        'MANIALI',
        'NADIF',
        'RABOUZE', 'REDOUANE', 'REGRAGUI', 'RHIZLANE',
        'SADKI', 'SAOUDI', 'SAYADI', 'SEDRAOUI',
        'THABIT', 'TIJANI', 'TILAOUI',
        'YOUSSFI', 'YOUNES',
    ];

    /**
     * Moroccan first names pool split by gender.
     * @var array<string, list<string>>
     */
    private static array $prenomsH = [
        'ABDELGHANI', 'ABDELHAMID', 'ACHRAF', 'AHMED', 'AMIR', 'ANWAR',
        'AYOUB', 'AZZEDINE',
        'BILAL',
        'FAISSAL', 'FOUAD',
        'HAMZA', 'HASSAN', 'HICHAM',
        'IMAD', 'ISMAIL',
        'KARIM', 'KHALID',
        'MEHDI', 'MOHAMED', 'MORAD', 'MOUHSSINE',
        'NABIL',
        'OMAR',
        'RACHID', 'REDOUANE',
        'SAMIR', 'SALIM',
        'TAHA',
        'WADIA',
        'YASSINE', 'YASSIR',
        'ZAKARIAE', 'ZOHAIR',
    ];

    private static array $prenomsF = [
        'AMAL', 'AYA', 'AOUATIF', 'AZZIZA',
        'CHAIMAA', 'CHERIFA',
        'FADWA', 'FATIMA', 'FATIMA-ZAHRA', 'FATIMA-EZZAHRA',
        'HALIMA', 'HASSNA', 'HASNA',
        'IBTISSAM', 'IMANE',
        'JAMILA',
        'KHADIJA',
        'LAILA', 'LAMIAE',
        'MERIEM', 'MINA',
        'NEZHA',
        'SAMIRA', 'SOUKAINA',
        'ZAHIRA',
    ];

    public function definition(): array
    {
        $isFemale = $this->faker->boolean(40);
        $prenom   = $isFemale
            ? $this->faker->randomElement(self::$prenomsF)
            : $this->faker->randomElement(self::$prenomsH);
        $nom      = $this->faker->randomElement(self::$noms);

        // MLE follows real numeric pattern: 5-digit integers in range 16000–25000
        $mle      = (string) $this->faker->unique()->numberBetween(16000, 25000);

        // Email pattern: PRENOM.NOM@ofppt-edu.ma (strip spaces/hyphens in name parts)
        $emailPrenom = str_replace([' ', '-'], '', $prenom);
        $emailNom    = str_replace([' ', '-'], '', $nom);
        $email       = strtolower("{$emailPrenom}.{$emailNom}") . '@ofppt-edu.ma';

        // Statut: OFPPT for most, occasionally Vacataire or Contractuel
        $statut = $this->faker->randomElement([
            'OFPPT', 'OFPPT', 'OFPPT', 'OFPPT', 'OFPPT',  // 5/7 chance
            'Vacataire',
            'Contractuel',
        ]);

        // MHS: 26 for OFPPT (standard), lower for vacataires
        $mhs = $statut === 'OFPPT'
            ? 26
            : $this->faker->randomElement([16, 20, 24]);

        $mutualise = $this->faker->boolean(35);

        return [
            'mle'           => $mle,
            'pole_id'       => Pole::factory(),
            'nom_prenom'    => "{$nom} {$prenom}",
            'statut'        => $statut,
            'email_edu'     => $email,
            'mhs'           => $mhs,
            'mutualise'     => $mutualise,
            'efp_mutualise' => $mutualise
                ? $this->faker->randomElement([
                    'CMC Béni Mellal-Pôle Digital et IA',
                    'CMC Béni Mellal-Pôle Gestion et Commerce',
                ])
                : null,
        ];
    }

    /** State: OFPPT permanent trainer (mhs=26, as in all real data). */
    public function ofppt(): static
    {
        return $this->state([
            'statut' => 'OFPPT',
            'mhs'    => 26,
        ]);
    }

    /** State: Vacataire (part-time external trainer). */
    public function vacataire(): static
    {
        return $this->state([
            'statut'        => 'Vacataire',
            'mhs'           => $this->faker->randomElement([16, 20, 24]),
            'mutualise'     => false,
            'efp_mutualise' => null,
        ]);
    }

    /** State: mutualisé formateur (teaches at a different pole from their home). */
    public function mutualise(): static
    {
        return $this->state([
            'mutualise'     => true,
            'efp_mutualise' => 'CMC Béni Mellal-Pôle Digital et IA',
        ]);
    }
}
