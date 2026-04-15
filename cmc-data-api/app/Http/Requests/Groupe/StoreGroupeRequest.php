<?php

namespace App\Http\Requests\Groupe;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'annee_id' => ['required', 'integer', 'exists:annees,id'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('groupes', 'code')->where(fn ($q) => $q->where('annee_id', $this->input('annee_id'))),
            ],
            'effectif' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

