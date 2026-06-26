<?php

namespace App\Http\Requests\Note;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for updating an existing Note (PATCH semantics).
 *
 * Typical update scenario: grading an already-created note row
 * (filling in valeur and/or decision after deliberation).
 * seance_id and stagiaire_cef are usually not changed after creation.
 */
class UpdateNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seance_id'     => ['sometimes', 'integer', 'exists:seances,id'],
            'stagiaire_cef' => ['sometimes', 'string', 'exists:stagiaires,cef'],
            'valeur'        => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:20'],
            'type'          => ['sometimes', 'nullable', 'string', 'in:cc,efm,tp,th,syn,exam'],
            'decision'      => ['sometimes', 'nullable', 'string', 'in:Admis,Redoublant,Abandon,Rattrapage'],
        ];
    }

    public function messages(): array
    {
        return [
            'seance_id.exists'       => 'Cette séance n\'existe pas.',
            'stagiaire_cef.exists'   => 'Ce stagiaire n\'existe pas.',
            'valeur.numeric'         => 'La note doit être un nombre.',
            'valeur.min'             => 'La note ne peut pas être négative.',
            'valeur.max'             => 'La note ne peut pas dépasser 20.',
            'type.in'                => 'Le type doit être : cc, efm, tp, th, syn ou exam.',
            'decision.in'            => 'La décision doit être : Admis, Redoublant, Abandon ou Rattrapage.',
        ];
    }
}
