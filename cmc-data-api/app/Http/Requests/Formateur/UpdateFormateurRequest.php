<?php

namespace App\Http\Requests\Formateur;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // NOTE: primary key `mle` is intentionally not updatable.
            'pole_id' => ['required', 'integer', 'exists:poles,id'],
            'nom_prenom' => ['required', 'string', 'max:255'],
            'statut' => ['nullable', 'string', 'max:255'],
            'email_edu' => ['nullable', 'string', 'max:255', 'email'],
            'mhs' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

