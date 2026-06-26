<?php

namespace Database\Seeders;

use App\Models\Niveau;
use App\Models\Pole;
use App\Models\TimeRange;
use App\Models\TypeFormation;
use Database\Factories\TimeRangeFactory;
use Illuminate\Database\Seeder;

/**
 * Seeds all lookup / reference tables from real CMC data.
 * These must be created first since every other table depends on them.
 *
 * Sources:
 *   - Poles    → Base_Formateurs.xlsx "Affectation" column
 *   - Niveaux  → AvancementProgramme "Niveau" column (TS = Technicien Spécialisé)
 *   - Types    → AvancementProgramme "Type de formation" column
 *   - Slots    → OFPPT standard daily schedule
 */
class ReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // ── Poles (EFPs) from CMC Béni Mellal-Khénifra ────────────────────────
        // Extracted from Base_Formateurs.xlsx "Affectation" column.
        // Using short canonical names; full names stored in description if needed.
        $poles = [
            ['libelle' => 'Pôle Digital et Intelligence Artificielle'],
            ['libelle' => 'Pôle Gestion et Commerce'],
            ['libelle' => 'Pôle Industrie'],
            ['libelle' => 'Pôle BTP et Génie Civil'],
            ['libelle' => 'Pôle Hôtellerie et Tourisme'],
        ];

        foreach ($poles as $data) {
            Pole::firstOrCreate(['libelle' => $data['libelle']]);
        }

        // ── Niveaux de formation ───────────────────────────────────────────────
        // From AvancementProgramme "Niveau" column. OFPPT qualification levels.
        $niveaux = [
            ['libelle' => 'TS'],   // Technicien Spécialisé (Bac+2)
            ['libelle' => 'T'],    // Technicien
            ['libelle' => 'Q'],    // Qualification
            ['libelle' => 'FQ'],   // Formation Qualifiante
            ['libelle' => 'SP'],   // Spécialisation
        ];

        foreach ($niveaux as $data) {
            Niveau::firstOrCreate(['libelle' => $data['libelle']]);
        }

        // ── Types de formation ─────────────────────────────────────────────────
        // From AvancementProgramme "Type de formation" column.
        $types = [
            ['libelle' => 'Diplômante'],
            ['libelle' => 'Qualifiante'],
            ['libelle' => 'Résidentielle'],
            ['libelle' => 'Alternance'],
        ];

        foreach ($types as $data) {
            TypeFormation::firstOrCreate(['libelle' => $data['libelle']]);
        }

        // ── Time ranges (OFPPT standard daily schedule) ───────────────────────
        // CDJ (Cours De Jour) standard slots used across all CMC groups.
        foreach (TimeRangeFactory::$officialSlots as [$start, $end]) {
            TimeRange::firstOrCreate(
                ['start_time' => $start, 'end_time' => $end]
            );
        }
    }
}
