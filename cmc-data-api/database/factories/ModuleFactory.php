<?php

namespace Database\Factories;

use App\Models\Annee;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Module> */
class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        $prefix = strtoupper(Str::random(3));

        return [
            'code_module' => $prefix . '-' . $this->faker->unique()->numberBetween(10, 9999),
            'annee_id' => Annee::factory(),
            'libelle' => $this->faker->unique()->sentence(3),
        ];
    }
}

