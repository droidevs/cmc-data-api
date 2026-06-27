<?php

namespace App\Http\Controllers\Web;

use App\Models\Affectation;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Note;
use App\Models\Pole;
use App\Models\Seance;
use App\Models\Stagiaire;
use Illuminate\Http\Request;

class DashboardController extends WebController
{
    public function __invoke(Request $request)
    {
        $stats = [
            'poles'       => Pole::count(),
            'formateurs'  => Formateur::count(),
            'stagiaires'  => Stagiaire::actif()->count(),
            'groupes'     => Groupe::count(),
            'affectations'=> Affectation::count(),
            'seances'      => Seance::count(),
            'notes'        => Note::count(),
            'notes_manquantes' => Note::whereNull('valeur')->count(),
        ];

        $recent_seances = Seance::with(['affectation.module', 'affectation.groupe', 'timeRange', 'espace'])
            ->whereDate('date', '>=', now()->subDays(7))
            ->orderBy('date', 'desc')
            ->limit(8)
            ->get();

        $poles_with_counts = Pole::withCount(['formateurs', 'filieres', 'espaces'])
            ->orderBy('id')
            ->get();

        return view('dashboard.index', compact('stats', 'recent_seances', 'poles_with_counts'));
    }
}
