<?php

namespace App\Http\Requests\Affectation;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for creating a new Affectation.
 *
 * An affectation links a Groupe to a Module and assigns one or two
 * formateurs (présentiel + synchrone). Both formateur slots are optional
 * because a module can be unassigned (pending) at import time.
 *
 * Mode values mirror the AvancementProgramme.xlsx "Mode" column:
 *   Résidentiel | Alternance
 *
 * Hours (mh_affecte / mh_affecte_syn) come from:
 *   "MH Affectée Présentiel" / "MH Affectée Sync" columns.
 *   They are nullable because unaffected modules have no hours yet.
 */
class StoreAffectationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'groupe_id'          => ['required', 'integer', 'exists:groupes,id'],
            'module_id'          => ['required', 'integer', 'exists:modules,id'],

            // Primary (présentiel) formateur — nullable: module may be unassigned
            'formateur_mle'      => ['nullable', 'string', 'exists:formateurs,mle'],

            // Synchronous formateur — only relevant when mode has a sync component
            'formateur_mle_syn'  => ['nullable', 'string', 'exists:formateurs,mle'],

            // Mode from AvancementProgramme: Résidentiel or Alternance
            'mode'               => ['nullable', 'string', 'in:Résidentiel,Alternance'],

            // Présentiel hours (MH Affectée Présentiel)
            'mh_affecte'         => ['nullable', 'numeric', 'min:0', 'max:9999.99'],

            // Synchronous hours (MH Affectée Sync)
            'mh_affecte_syn'     => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'groupe_id.required'   => 'Le groupe est obligatoire.',
            'groupe_id.exists'     => 'Ce groupe n\'existe pas.',
            'module_id.required' => 'Le module est obligatoire.',
            'module_id.exists'   => 'Ce module n\'existe pas.',
            'formateur_mle.exists' => 'Ce formateur (présentiel) n\'existe pas.',
            'formateur_mle_syn.exists' => 'Ce formateur (synchrone) n\'existe pas.',
            'mode.in'              => 'Le mode doit être Résidentiel ou Alternance.',
            'mh_affecte.numeric'   => 'Les heures présentiel doivent être un nombre.',
            'mh_affecte_syn.numeric' => 'Les heures synchrones doivent être un nombre.',
        ];
    }
}
