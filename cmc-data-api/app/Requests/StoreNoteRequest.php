<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for creating a new Note.
 *
 * Notes come from BasePlateEvaluation2025.xlsx. Key columns:
 *   TYPE D'EPREUVE → type  (BL = Bilan, maps to our: cc | efm | exam | tp | th | syn)
 *   TYPE DE COURS  → used to resolve session type (CDJ, SYN…)
 *   NOTE PASS      → valeur (0–20, 2 decimals)
 *   DECISION       → decision (Admis, Redoublant, Abandon…)
 *
 * Type values supported:
 *   cc   — Contrôle Continu (NB CC in AvancementProgramme)
 *   efm  — Épreuve de Fin de Module (Séance EFM column)
 *   tp   — Travaux Pratiques (TP column in BasePlateEvaluation)
 *   th   — Travaux d'Heures (TH column)
 *   syn  — Synchrone / SYN(Dip)/EXAMEN(Qual) column
 *   exam — Examen de passage (NOTE PASS)
 *
 * valeur is nullable: a note row can be created before grading occurs
 * (the student is enrolled in the session but not yet graded).
 *
 * decision values from DECISION column: Admis | Redoublant | Abandon | Rattrapage
 */
class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seance_id'     => ['required', 'integer', 'exists:seances,id'],
            'stagiaire_cef' => ['required', 'string', 'exists:stagiaires,cef'],

            // Grade: 0–20 with 2 decimal places; nullable until grading occurs
            'valeur'   => ['nullable', 'numeric', 'min:0', 'max:20'],

            // Note type derived from BasePlateEvaluation TYPE D'EPREUVE / session type
            'type'     => ['nullable', 'string', 'in:cc,efm,tp,th,syn,exam'],

            // DECISION column: result of the deliberation
            'decision' => ['nullable', 'string', 'in:Admis,Redoublant,Abandon,Rattrapage'],
        ];
    }

    public function messages(): array
    {
        return [
            'seance_id.required'     => 'La séance est obligatoire.',
            'seance_id.exists'       => 'Cette séance n\'existe pas.',
            'stagiaire_cef.required' => 'Le CEF du stagiaire est obligatoire.',
            'stagiaire_cef.exists'   => 'Ce stagiaire n\'existe pas.',
            'valeur.numeric'         => 'La note doit être un nombre.',
            'valeur.min'             => 'La note ne peut pas être négative.',
            'valeur.max'             => 'La note ne peut pas dépasser 20.',
            'type.in'                => 'Le type doit être : cc, efm, tp, th, syn ou exam.',
            'decision.in'            => 'La décision doit être : Admis, Redoublant, Abandon ou Rattrapage.',
        ];
    }
}
