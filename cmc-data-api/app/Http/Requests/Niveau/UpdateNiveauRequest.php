<?php

namespace App\Http\Requests\Niveau;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNiveauRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $niveauId = $this->route('niveau')?->getKey() ?? $this->route('niveau');

        return [
            'libelle' => ['required', 'string', 'max:255', Rule::unique('niveaux', 'libelle')->ignore($niveauId)],
        ];
    }
}

