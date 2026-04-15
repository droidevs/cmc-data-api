<?php

namespace App\Http\Requests\Annee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnneeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filiere_code' => ['required', 'string', 'max:32', 'exists:filieres,code_filiere'],
            'libelle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('annees', 'libelle')->where(fn ($q) => $q->where('filiere_code', $this->input('filiere_code'))),
            ],
        ];
    }
}

