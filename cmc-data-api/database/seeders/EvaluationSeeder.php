<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Seance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Seeds Notes for stagiaires attached to evaluable seances.
 *
 * Data-integrity fixes vs. the previous version:
 *   - Only seances whose `type` is actually an evaluation type (cc, efm, exam)
 *     get Notes. The previous version graded every seance regardless of type,
 *     including plain "cours" sessions, which don't carry a grade in the real
 *     workflow.
 *   - valeur/decision are produced together and kept coherent (valeur >= 10
 *     implies "Admis"), the same rule NoteFactory enforces, so a re-run via
 *     the factory directly (e.g. in tests) stays consistent with what this
 *     seeder inserts.
 *   - Inserts are batched per seance with Note::insert() instead of one
 *     query per stagiaire, since EvaluationSeeder can produce thousands of
 *     rows once PlanningSeeder has run with real group sizes.
 */
class EvaluationSeeder extends Seeder
{
    /** Seance types that actually carry a grade. */
    private const EVALUABLE_TYPES = ['cc', 'efm', 'exam'];

    public function run(): void
    {
        $now = Carbon::now();

        Seance::query()
            ->whereIn('type', self::EVALUABLE_TYPES)
            ->with(['affectation.groupe.stagiaires'])
            ->get()
            ->each(function (Seance $seance) use ($now) {
                $stagiaires = $seance->affectation?->groupe?->stagiaires;
                if (! $stagiaires || $stagiaires->isEmpty()) {
                    return;
                }

                $rows = $stagiaires->map(function ($stagiaire) use ($seance, $now) {
                    // ~70% admis / 30% redoublant, matching the real-data
                    // pass-rate distribution this dataset is modelled on.
                    $isAdmis = random_int(1, 100) <= 70;

                    $valeur = $isAdmis
                        ? round(random_int(1000, 2000) / 100, 2)  // 10.00–20.00
                        : round(random_int(0, 999) / 100, 2);     // 0.00–9.99

                    return [
                        'seance_id'     => $seance->getKey(),
                        'stagiaire_cef' => $stagiaire->getKey(),
                        'valeur'        => $valeur,
                        'type'          => $seance->type,
                        'decision'      => $isAdmis ? 'Admis' : 'Redoublant',
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                })->all();

                Note::insert($rows);
            });
    }
}
