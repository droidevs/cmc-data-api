<?php

namespace App\Http\Requests\TypeFormation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTypeFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $typeFormationId = $this->route('type_formation')?->getKey() ?? $this->route('type_formation');

        return [
            'libelle' => ['required', 'string', 'max:255', Rule::unique('type_formations', 'libelle')->ignore($typeFormationId)],
        ];
    }
}

