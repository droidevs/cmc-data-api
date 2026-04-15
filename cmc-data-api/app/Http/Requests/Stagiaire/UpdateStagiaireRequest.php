<?php

namespace App\Http\Requests\Stagiaire;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStagiaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $stagiaireCef = $this->route('stagiaire')?->getKey() ?? $this->route('stagiaire');

        return [
            // NOTE: primary key `cef` is intentionally not updatable.
            'groupe_id' => ['required', 'integer', 'exists:groupes,id'],
            'cni' => ['required', 'string', 'max:32', Rule::unique('stagiaires', 'cni')->ignore($stagiaireCef, 'cef')],
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date'],
            'genre' => ['nullable', 'string', 'max:16', Rule::in(['M', 'F'])],
        ];
    }
}

