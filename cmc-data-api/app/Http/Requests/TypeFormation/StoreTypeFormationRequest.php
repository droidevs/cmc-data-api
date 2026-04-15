<?php

namespace App\Http\Requests\TypeFormation;

use Illuminate\Foundation\Http\FormRequest;

class StoreTypeFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle' => ['required', 'string', 'max:255', 'unique:type_formations,libelle'],
        ];
    }
}

