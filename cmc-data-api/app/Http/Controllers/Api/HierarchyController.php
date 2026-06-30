<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\Annee;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Niveau;
use App\Models\Pole;
use App\Models\Seance;
use App\Models\Stagiaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HierarchyController
{
    public function getPoles(): JsonResponse
    {
        $poles = Pole::orderBy('libelle')->get(['id', 'libelle']);
        return response()->json($poles);
    }

    public function getFilieres(Request $request): JsonResponse
    {
        $query = Filiere::query();

        if ($request->has('pole_id')) {
            $query->where('pole_id', $request->query('pole_id'));
        }

        $filieres = $query->orderBy('libelle')->get(['code_filiere', 'libelle', 'pole_id']);
        return response()->json($filieres);
    }

    public function getAnnees(Request $request): JsonResponse
    {
        $query = Annee::query();

        if ($request->has('filiere_code')) {
            $query->where('filiere_code', $request->query('filiere_code'));
        } elseif ($request->has('pole_id')) {
            $query->whereHas('filiere', fn($q) => $q->where('pole_id', $request->query('pole_id')));
        }

        $annees = $query->orderBy('libelle')->get(['id', 'libelle', 'filiere_code']);

        $formatted = $annees->map(fn ($annee) => [
            'id'           => $annee->id,
            'libelle'      => $annee->libelle,
            'label'        => $annee->libelle,
            'filiere_code' => $annee->filiere_code
        ]);

        return response()->json($formatted);
    }

    public function getGroupes(Request $request): JsonResponse
    {
        $query = Groupe::query();

        if ($request->has('annee_id')) {
            $query->where('annee_id', $request->query('annee_id'));
        } elseif ($request->has('filiere_code')) {
            $query->whereHas('annee', fn($q) => $q->where('filiere_code', $request->query('filiere_code')));
        } elseif ($request->has('pole_id')) {
            $query->whereHas('annee.filiere', fn($q) => $q->where('pole_id', $request->query('pole_id')));
        }

        $groupes = $query->orderBy('code')->get(['id', 'code', 'annee_id', 'effectif', 'mode']);
        return response()->json($groupes);
    }

    public function getModules(Request $request): JsonResponse
    {
        $query = Module::query();

        if ($request->has('annee_id')) {
            $query->where('annee_id', $request->query('annee_id'));
        } elseif ($request->has('groupe_id')) {
            $groupe = Groupe::find($request->query('groupe_id'));
            if ($groupe) {
                $query->where('annee_id', $groupe->annee_id);
            } else {
                return response()->json([]);
            }
        } elseif ($request->has('filiere_code')) {
            $query->whereHas('annee', fn($q) => $q->where('filiere_code', $request->query('filiere_code')));
        }

        $modules = $query->orderBy('libelle')->get(['id', 'code_module', 'libelle', 'annee_id']);
        return response()->json($modules);
    }

    public function getStagiaires(Request $request): JsonResponse
    {
        $query = Stagiaire::query();

        if ($request->has('groupe_id')) {
            $query->where('groupe_id', $request->query('groupe_id'));
        }

        if ($request->has('q')) {
            $term = (string) $request->query('q');
            $query->where(function ($q) use ($term) {
                $q->where('nom', 'like', "%{$term}%")
                  ->orWhere('prenom', 'like', "%{$term}%")
                  ->orWhere('cef', 'like', "%{$term}%");
            });
        }

        // Limit results if we're doing a general search to prevent memory exhaustion
        if (!$request->has('groupe_id') && $request->has('q')) {
            $query->limit(50);
        }

        $stagiaires = $query->orderBy('nom')->get(['cef', 'nom', 'prenom', 'groupe_id']);

        $formatted = $stagiaires->map(fn ($s) => [
            'cef' => $s->cef,
            'nom' => $s->nom,
            'prenom' => $s->prenom,
            'nom_complet' => $s->nom_complet, // Calls nomComplet accessor
            'groupe_id' => $s->groupe_id
        ]);

        return response()->json($formatted);
    }

    public function getSeances(Request $request): JsonResponse
    {
        $query = Seance::with(['affectation.module', 'affectation.groupe', 'timeRange']);

        if ($request->has('groupe_id')) {
            $query->whereHas('affectation', fn($q) => $q->where('groupe_id', $request->query('groupe_id')));
        }

        if ($request->has('module_id')) {
            $query->whereHas('affectation', fn($q) => $q->where('module_id', $request->query('module_id')));
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->query('date'));
        }

        $seances = $query->orderBy('date', 'desc')->get();

        $formatted = $seances->map(fn ($seance) => [
            'id' => $seance->id,
            'date' => $seance->date?->format('Y-m-d'),
            'formatted_date' => $seance->date?->format('d/m/Y'),
            'type' => $seance->type?->value ?? $seance->type,
            'is_evaluable' => $seance->is_evaluable,
            'time_range' => $seance->timeRange ? [
                'id' => $seance->timeRange->id,
                'start_time' => $seance->timeRange->start_time,
                'end_time' => $seance->timeRange->end_time,
            ] : null,
            'module' => $seance->affectation?->module ? [
                'id' => $seance->affectation->module->id,
                'code_module' => $seance->affectation->module->code_module,
                'libelle' => $seance->affectation->module->libelle,
            ] : null,
            'groupe' => $seance->affectation?->groupe ? [
                'id' => $seance->affectation->groupe->id,
                'code' => $seance->affectation->groupe->code,
            ] : null,
        ]);

        return response()->json($formatted);
    }

    public function resolveStagiaire(string $cef): JsonResponse
    {
        $stagiaire = Stagiaire::with('groupe.annee.filiere')->find($cef);
        if (!$stagiaire) {
            return response()->json(['error' => 'Stagiaire introuvable'], 404);
        }

        $groupe = $stagiaire->groupe;
        $annee = $groupe?->annee;
        $filiere = $annee?->filiere;

        return response()->json([
            'cef' => $stagiaire->cef,
            'nom' => $stagiaire->nom,
            'prenom' => $stagiaire->prenom,
            'nom_complet' => $stagiaire->nom_complet,
            'groupe_id' => $stagiaire->groupe_id,
            'annee_id' => $annee?->id,
            'filiere_code' => $filiere?->code_filiere,
            'pole_id' => $filiere?->pole_id,
        ]);
    }

    public function resolveGroupe(int $id): JsonResponse
    {
        $groupe = Groupe::with('annee.filiere')->find($id);
        if (!$groupe) {
            return response()->json(['error' => 'Groupe introuvable'], 404);
        }

        $annee = $groupe->annee;
        $filiere = $annee?->filiere;

        return response()->json([
            'groupe_id' => $groupe->id,
            'code' => $groupe->code,
            'annee_id' => $annee?->id,
            'filiere_code' => $filiere?->code_filiere,
            'pole_id' => $filiere?->pole_id,
        ]);
    }

    public function getNiveaux(): JsonResponse
    {
        $niveaux = Niveau::orderBy('libelle')->get(['id', 'libelle']);
        return response()->json($niveaux);
    }
}
