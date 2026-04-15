<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_module' => ['required', 'string', 'max:32', 'unique:modules,code_module'],
            'annee_id' => ['required', 'integer', 'exists:annees,id'],
            'libelle' => ['required', 'string', 'max:255'],
        ];
    }
}

