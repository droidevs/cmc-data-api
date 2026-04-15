<?php

namespace Database\Factories;

use App\Models\Affectation;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Affectation> */
class AffectationFactory extends Factory
{
    protected $model = Affectation::class;

    public function configure(): static
    {
        return $this->afterMaking(function (Affectation $affectation) {
            // no-op; place for future invariants
        });
    }

    public function definition(): array
    {
        return [
            'groupe_id' => Groupe::factory(),
            'module_code' => Module::factory(),
            'formateur_mle' => Formateur::factory(),
            'mode' => $this->faker->randomElement(['presentiel', 'synchrone', 'async']),
            'mh_affecte' => $this->faker->randomFloat(2, 10, 120),
        ];
    }

    public function presentiel(): static
    {
        return $this->state(fn () => ['mode' => 'presentiel']);
    }

    public function synchrone(): static
    {
        return $this->state(fn () => ['mode' => 'synchrone']);
    }

    public function async(): static
    {
        return $this->state(fn () => ['mode' => 'async']);
    }

    /**
     * Ensure module.annee_id matches groupe.annee_id for coherent planning.
     */
    public function coherentAnnee(): static
    {
        return $this->afterCreating(function (Affectation $affectation) {
            // If module and groupe belong to different annees, regenerate module for groupe's annee.
            $groupe = $affectation->groupe()->first();
            $module = $affectation->module()->first();

            if ($groupe && $module && $groupe->annee_id !== $module->annee_id) {
                $newModule = Module::factory()->create(['annee_id' => $groupe->annee_id]);
                $affectation->updateQuietly(['module_code' => $newModule->getKey()]);
            }
        });
    }
}


