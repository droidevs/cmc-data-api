<?php

namespace App\Http\Requests\Espace;

use Illuminate\Foundation\Http\FormRequest;

class StoreEspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pole_id' => ['required', 'integer', 'exists:poles,id'],
            'libelle' => ['required', 'string', 'max:255'],
        ];
    }
}

