<?php

namespace Database\Factories;

use App\Models\Pole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Espace (physical room) factory for CMC Béni Mellal.
 *
 * A CMC typically has:
 *   - Salles de cours (classrooms)       capacity ~25-30
 *   - Laboratoires informatiques (labs)  capacity ~20-25
 *   - Ateliers (workshops)               capacity ~15-20
 *   - Amphithéâtres                      capacity ~60-120
 *   - Salles de réunion                  capacity ~10-15
 * Some spaces have no capacity limit defined (nullable).
 */
class EspaceFactory extends Factory
{
    /** @var list<array{prefix: string, min: int, max: int, nullable_cap: bool}> */
    private static array $types = [
        ['prefix' => 'Salle',         'min' => 25, 'max' => 32,  'nullable_cap' => false],
        ['prefix' => 'Lab Info',      'min' => 20, 'max' => 25,  'nullable_cap' => false],
        ['prefix' => 'Atelier',       'min' => 15, 'max' => 22,  'nullable_cap' => false],
        ['prefix' => 'Amphithéâtre',  'min' => 60, 'max' => 120, 'nullable_cap' => false],
        ['prefix' => 'Salle de conf', 'min' => 10, 'max' => 18,  'nullable_cap' => false],
        ['prefix' => 'Lab Réseau',    'min' => 16, 'max' => 20,  'nullable_cap' => false],
        ['prefix' => 'Lab Sécurité',  'min' => 16, 'max' => 20,  'nullable_cap' => false],
        ['prefix' => 'Espace projet', 'min' => 12, 'max' => 16,  'nullable_cap' => true],
    ];

    public function definition(): array
    {
        $type  = $this->faker->randomElement(self::$types);
        $num   = $this->faker->numberBetween(1, 12);
        $label = sprintf('%s %02d', $type['prefix'], $num);

        return [
            'pole_id'  => Pole::factory(),
            'libelle'  => $label,
            'capacite' => $type['nullable_cap'] && $this->faker->boolean(20)
                ? null
                : $this->faker->numberBetween($type['min'], $type['max']),
        ];
    }

    /** State: a computer lab (matching the DIA pole usage). */
    public function labInfo(): static
    {
        $num = $this->faker->numberBetween(1, 8);

        return $this->state([
            'libelle'  => sprintf('Lab Informatique %02d', $num),
            'capacite' => $this->faker->numberBetween(20, 25),
        ]);
    }

    /** State: a standard classroom. */
    public function salle(): static
    {
        $num = $this->faker->numberBetween(1, 20);

        return $this->state([
            'libelle'  => sprintf('Salle %02d', $num),
            'capacite' => $this->faker->numberBetween(25, 32),
        ]);
    }

    /** State: an amphitheatre. */
    public function amphi(): static
    {
        return $this->state([
            'libelle'  => 'Amphithéâtre A',
            'capacite' => $this->faker->numberBetween(80, 120),
        ]);
    }
}
