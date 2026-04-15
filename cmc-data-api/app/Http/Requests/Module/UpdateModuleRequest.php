<?php

namespace App\Http\Requests\Module;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // NOTE: primary key `code_module` is intentionally not updatable.
            'annee_id' => ['required', 'integer', 'exists:annees,id'],
            'libelle' => ['required', 'string', 'max:255'],
        ];
    }
}

