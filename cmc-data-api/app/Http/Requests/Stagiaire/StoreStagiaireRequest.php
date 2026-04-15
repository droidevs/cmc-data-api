<?php

namespace App\Http\Requests\Stagiaire;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStagiaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cef' => ['required', 'string', 'max:32', 'unique:stagiaires,cef'],
            'groupe_id' => ['required', 'integer', 'exists:groupes,id'],
            'cni' => ['required', 'string', 'max:32', 'unique:stagiaires,cni'],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date'],
            'genre' => ['nullable', 'string', 'max:16', Rule::in(['M', 'F'])],
        ];
    }
}

