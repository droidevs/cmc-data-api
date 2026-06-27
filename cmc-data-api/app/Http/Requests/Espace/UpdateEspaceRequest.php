<?php

namespace App\Http\Requests\Espace;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for updating an existing Espace (PATCH semantics).
 * All fields optional — only send what you want to change.
 */
class UpdateEspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pole_id'  => ['sometimes', 'integer', 'exists:poles,id'],
            'libelle'  => ['sometimes', 'string', 'max:150'],
            'capacite' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pole_id.exists'   => 'Ce pôle n\'existe pas.',
            'capacite.integer' => 'La capacité doit être un nombre entier.',
            'capacite.min'     => 'La capacité ne peut pas être négative.',
        ];
    }
}
