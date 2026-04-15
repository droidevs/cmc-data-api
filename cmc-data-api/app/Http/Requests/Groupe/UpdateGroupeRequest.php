<?php

namespace App\Http\Requests\Groupe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGroupeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $groupeId = $this->route('groupe')?->getKey() ?? $this->route('groupe');

        return [
            'annee_id' => ['required', 'integer', 'exists:annees,id'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('groupes', 'code')
                    ->where(fn ($q) => $q->where('annee_id', $this->input('annee_id')))
                    ->ignore($groupeId),
            ],
            'effectif' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

