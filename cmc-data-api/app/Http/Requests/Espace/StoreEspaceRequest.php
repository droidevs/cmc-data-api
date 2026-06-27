<?php

namespace App\Http\Requests\Espace;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for creating a new Espace (physical room / lab / workshop).
 *
 * capacite is nullable: null means no physical capacity limit is defined
 * (mirrors Espace::scopeWithCapacityFor()'s whereNull('capacite') handling).
 */
class StoreEspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pole_id'  => ['required', 'integer', 'exists:poles,id'],
            'libelle'  => ['required', 'string', 'max:150'],
            'capacite' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pole_id.required' => 'Le pôle est obligatoire.',
            'pole_id.exists'   => 'Ce pôle n\'existe pas.',
            'libelle.required' => 'Le libellé est obligatoire.',
            'capacite.integer'  => 'La capacité doit être un nombre entier.',
            'capacite.min'      => 'La capacité ne peut pas être négative.',
        ];
    }
}
