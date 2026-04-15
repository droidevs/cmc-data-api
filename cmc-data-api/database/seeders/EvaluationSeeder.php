<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Seance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        Seance::query()
            ->with(['affectation.groupe.stagiaires'])
            ->get()
            ->each(function (Seance $seance) {
                $stagiaires = $seance->affectation?->groupe?->stagiaires;
                if (!$stagiaires || $stagiaires->isEmpty()) {
                    return;
                }

                $type = Arr::random(['cc', 'efm', 'exam']);

                foreach ($stagiaires as $stagiaire) {
                    Note::factory()->create([
                        'seance_id' => $seance->getKey(),
                        'stagiaire_cef' => $stagiaire->getKey(),
                        'type' => $type,
                    ]);
                }
            });
    }
}

