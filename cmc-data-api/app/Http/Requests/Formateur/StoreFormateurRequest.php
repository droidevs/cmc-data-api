<?php

namespace App\Http\Requests\Formateur;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mle' => ['required', 'string', 'max:32', 'unique:formateurs,mle'],
            'pole_id' => ['required', 'integer', 'exists:poles,id'],
            'nom_prenom' => ['required', 'string', 'max:255'],
            'statut' => ['nullable', 'string', 'max:255'],
            'email_edu' => ['nullable', 'string', 'max:255', 'email'],
            'mhs' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

