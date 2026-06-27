<?php

namespace App\Http\Requests\Affectation;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for updating an existing Affectation (PATCH semantics).
 *
 * All fields are optional — only send what you want to change.
 * groupe_id and module_code can be changed but must still reference
 * valid records when provided.
 */
class UpdateAffectationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'groupe_id'         => ['sometimes', 'integer', 'exists:groupes,id'],
            'module_id'         => ['sometimes', 'integer', 'exists:modules,id'],
            'formateur_mle'     => ['sometimes', 'nullable', 'string', 'exists:formateurs,mle'],
            'formateur_mle_syn' => ['sometimes', 'nullable', 'string', 'exists:formateurs,mle'],
            'mode'              => ['sometimes', 'nullable', 'string', 'in:Résidentiel,Alternance'],
            'mh_affecte'        => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:9999.99'],
            'mh_affecte_syn'    => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:9999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'groupe_id.exists'         => 'Ce groupe n\'existe pas.',
            'module_id.exists'         => 'Ce module n\'existe pas.',
            'formateur_mle.exists'     => 'Ce formateur (présentiel) n\'existe pas.',
            'formateur_mle_syn.exists' => 'Ce formateur (synchrone) n\'existe pas.',
            'mode.in'                  => 'Le mode doit être Résidentiel ou Alternance.',
        ];
    }
}
