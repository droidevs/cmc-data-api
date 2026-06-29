<?php

namespace App\Http\Requests\Note;

use App\Models\Note;
use App\Models\Seance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'decision'      => ['sometimes', 'nullable', 'string', 'in:Admis,Redoublant,Abandon,Rattrapage'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $note = $this->route('note');
            $seanceId = $this->input('seance_id', $note?->seance_id);
            $seance = Seance::find($seanceId);

            if ($seance && ! in_array($seance->type?->value ?? $seance->type, \App\Enums\NoteType::evaluable(), true)) {
                $validator->errors()->add(
                    'seance_id',
                    'Impossible de modifier une note pour une séance de cours. Choisissez une séance CC ou EFM.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'seance_id.exists'       => 'Cette séance n\'existe pas.',
            'stagiaire_cef.exists'   => 'Ce stagiaire n\'existe pas.',
            'valeur.numeric'         => 'La note doit être un nombre.',
            'valeur.min'             => 'La note ne peut pas être négative.',
            'valeur.max'             => 'La note ne peut pas dépasser 20.',
            'decision.in'            => 'La décision doit être : Admis, Redoublant, Abandon ou Rattrapage.',
        ];
    }
}
