<?php

namespace App\Http\Requests\Filiere;

use Illuminate\Foundation\Http\FormRequest;

class StoreFiliereRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_filiere' => ['required', 'string', 'max:32', 'unique:filieres,code_filiere'],
            'pole_id' => ['required', 'integer', 'exists:poles,id'],
            'niveau_id' => ['required', 'integer', 'exists:niveaux,id'],
            'type_formation_id' => ['required', 'integer', 'exists:type_formations,id'],
            'libelle' => ['required', 'string', 'max:255'],
        ];
    }
}

