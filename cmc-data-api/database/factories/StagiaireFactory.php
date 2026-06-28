<?php

namespace Database\Factories;

use App\Models\Groupe;
use App\Models\Stagiaire;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Stagiaire> */
class StagiaireFactory extends Factory
{
    protected $model = Stagiaire::class;

    public function definition(): array
    {
        // Genre enum: Femme = 'F', Homme = 'H' (NOT 'M')
        $genre = $this->faker->randomElement(['F', 'H']);

        $nom    = strtoupper($this->faker->lastName());
        $prenom = $this->faker->firstName($genre === 'H' ? 'male' : 'female');

        // CEF format mirrors real data: 13-digit numeric string
        $cef = (string) $this->faker->unique()->numerify('200#########');

        return [
            'cef'            => $cef,
            'groupe_id'      => fn () => Groupe::inRandomOrder()->first()?->id ?: Groupe::factory(),
            'cni'            => strtoupper($this->faker->unique()->bothify('??######')),
            'nom'            => $nom,
            'prenom'         => $prenom,
            'nom_arabe'      => null,   // populated by real-data seeder
            'prenom_arabe'   => null,
            'date_naissance' => $this->faker->dateTimeBetween('-26 years', '-16 years')->format('Y-m-d'),
            // Genre enum: 'F' = Femme, 'H' = Homme
            'genre'          => $genre,
            'telephone'      => $this->faker->numerify('06########'),
            'adresse'        => $this->faker->address(),
            'niveau_scolaire' => $this->faker->randomElement([
                "Baccalauréat",
                "3ème année Secondaire Collégial",
                "DEUG",
            ]),
            'actif'          => true,
        ];
    }

    public function femme(): static
    {
        return $this->state(fn () => [
            'genre'  => 'F',
            'prenom' => $this->faker->firstName('female'),
        ]);
    }

    public function homme(): static
    {
        return $this->state(fn () => [
            'genre'  => 'H',
            'prenom' => $this->faker->firstName('male'),
        ]);
    }

    public function inactif(): static
    {
        return $this->state(fn () => ['actif' => false]);
    }
}
