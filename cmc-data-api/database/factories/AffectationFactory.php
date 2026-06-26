<?php

namespace Database\Factories;

use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Affectation factory based on real data from AvancementProgramme2025.xlsx.
 *
 * Real MH Affectée Présentiel: range 15–105, mean ≈55, median 45.
 * Real MH Affectée Sync:       range 0–30,   mean ≈13, median 15.
 *
 * Common hour pairs observed in real data:
 *   (70, 15), (75, 25), (45, 20), (30, 10), (100, 20), (85, 25),
 *   (15, 0), (40, 5), (72, 0), (80, 20), (105, 30)
 *
 * Some affectations have null formateur (module not yet assigned) — matches
 * Excel rows where "Mle Affecté Présentiel Actif" is NaN.
 *
 * formateur_mle_syn is often the same as formateur_mle (same person delivers
 * both in-person and online). Sometimes different, sometimes null.
 *
 * Mode: real dataset only ever contains "Résidentiel" / "Alternance" — the
 * model never has a "synchrone"/"async" mode (that distinction lives at the
 * hours level via mh_affecte vs mh_affecte_syn, not on `mode` itself).
 */
class AffectationFactory extends Factory
{
    /**
     * Realistic MH pairs (présentiel, sync) from real data.
     * @var list<array{0: float, 1: float}>
     */
    private static array $mhPairs = [
        [15.0,   0.0],
        [30.0,  10.0],
        [40.0,   5.0],
        [45.0,  20.0],
        [70.0,  15.0],
        [72.0,   0.0],
        [75.0,  25.0],
        [80.0,  20.0],
        [85.0,  25.0],
        [100.0, 20.0],
        [105.0, 30.0],
        [22.5,   0.0],
        [60.0,  15.0],
        [90.0,  20.0],
    ];

    public function definition(): array
    {
        [$mhP, $mhS] = $this->faker->randomElement(self::$mhPairs);

        // 15% of affectations have no formateur assigned yet (like real data NaN rows)
        $hasFormateur = $this->faker->boolean(85);

        $formateurMle    = null;
        $formateurMleSyn = null;

        if ($hasFormateur) {
            $formateurMle = Formateur::factory();

            // Syn trainer: 70% same person, 20% different person, 10% null
            $synChoice = $this->faker->numberBetween(1, 10);
            if ($synChoice <= 7) {
                // Same formateur handles both presentiel and syn
                $formateurMleSyn = $formateurMle;
            } elseif ($synChoice <= 9) {
                // Different formateur for syn sessions
                $formateurMleSyn = Formateur::factory();
            }
            // else null — no syn trainer
        }

        return [
            'groupe_id'         => Groupe::factory(),
            'module_code'       => Module::factory(),
            'formateur_mle'     => $formateurMle,
            'formateur_mle_syn' => $formateurMleSyn,
            'mode'              => $this->faker->randomElement(['Résidentiel', 'Alternance']),
            'mh_affecte'        => $hasFormateur ? $mhP : null,
            'mh_affecte_syn'    => $hasFormateur ? $mhS : null,
        ];
    }

    /** State: assigned affectation (formateur set, hours set). */
    public function assigned(): static
    {
        [$mhP, $mhS] = $this->faker->randomElement(self::$mhPairs);

        return $this->state([
            'formateur_mle'     => Formateur::factory()->ofppt(),
            'formateur_mle_syn' => Formateur::factory()->ofppt(),
            'mh_affecte'        => $mhP,
            'mh_affecte_syn'    => $mhS,
        ]);
    }

    /** State: unassigned (pending) affectation — matches NaN rows in Excel. */
    public function unassigned(): static
    {
        return $this->state([
            'formateur_mle'     => null,
            'formateur_mle_syn' => null,
            'mh_affecte'        => null,
            'mh_affecte_syn'    => null,
        ]);
    }

    /** State: same formateur for both presentiel and syn (most common in real data). */
    public function sameFormateur(): static
    {
        [$mhP, $mhS] = $this->faker->randomElement(self::$mhPairs);
        $mle = Formateur::factory()->ofppt();

        return $this->state([
            'formateur_mle'     => $mle,
            'formateur_mle_syn' => $mle,
            'mh_affecte'        => $mhP,
            'mh_affecte_syn'    => $mhS,
        ]);
    }

    /** State: no synchronous component (sync hours = 0, no syn trainer). */
    public function presentielOnly(): static
    {
        $mhP = $this->faker->randomElement([30, 45, 70, 80, 100]);

        return $this->state([
            'formateur_mle_syn' => null,
            'mh_affecte'        => (float) $mhP,
            'mh_affecte_syn'    => 0.0,
        ]);
    }

    /**
     * State: keeps groupe_id / module_code coherent by picking a Module that
     * actually belongs to the same Annee as the Groupe being affected.
     *
     * Without this, the default factory wires a brand-new random Groupe and
     * a brand-new random Module independently, which can pair a 1ère année
     * groupe with a 2ème année module (or one from an unrelated filiere) —
     * a data-integrity break the real dataset never has, since a module is
     * only ever taught to groups of its own Annee.
     *
     * IMPORTANT (Laravel factory state ordering): arguments passed to the
     * final ->create([...]) call are merged in *after* all ->state(...)
     * closures run, so a state closure can never see a groupe_id supplied
     * via ->create(). To pin a specific Groupe, pass it via ->state(...)
     * (or pass the model in) BEFORE calling coherentAnnee():
     *
     *   Affectation::factory()
     *       ->state(['groupe_id' => $groupe->id])
     *       ->coherentAnnee()
     *       ->create(['formateur_mle' => $formateur->mle]);
     *
     * If no groupe_id has been pinned yet, this picks one fresh Annee and
     * derives both a coherent Groupe and Module from it.
     */
    public function coherentAnnee(): static
    {
        return $this->state(function (array $attributes) {
            $groupeId = $attributes['groupe_id'] ?? null;

            $anneeId = null;
            if ($groupeId instanceof Groupe) {
                $anneeId = $groupeId->annee_id;
            } elseif (is_int($groupeId) || is_string($groupeId)) {
                $anneeId = Groupe::query()->find($groupeId)?->annee_id;
            }
            // If groupeId is a Factory instance (still unresolved) or null,
            // we can't introspect it yet — fall through to the fresh-annee path.

            if (! $anneeId) {
                $annee = \App\Models\Annee::factory()->create();
                $anneeId = $annee->id;

                return [
                    'groupe_id'   => Groupe::factory()->state(['annee_id' => $anneeId]),
                    'module_code' => Module::factory()->state(['annee_id' => $anneeId]),
                ];
            }

            return [
                'module_code' => Module::factory()->state(['annee_id' => $anneeId]),
            ];
        });
    }
}
