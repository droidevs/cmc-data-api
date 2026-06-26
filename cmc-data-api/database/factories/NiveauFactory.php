<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Niveau represents the OFPPT qualification level.
 * The only level observed in the Excel data is "TS" (Technicien Spécialisé),
 * but the full OFPPT catalogue includes: FQ, Q, T, TS, BT, BAC+2, BAC+3.
 */
class NiveauFactory extends Factory
{
    public function definition(): array
    {
        static $niveaux = [
            'TS',   // Technicien Spécialisé (only level in Excel data)
            'T',    // Technicien
            'Q',    // Qualification
            'FQ',   // Formation Qualifiante
        ];

        return [
            'libelle' => $this->faker->randomElement($niveaux),
        ];
    }

    /** State: only Technicien Spécialisé (matches all real data). */
    public function ts(): static
    {
        return $this->state(['libelle' => 'TS']);
    }
}
