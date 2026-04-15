<?php

namespace App\Http\Requests\Note;

use App\Http\Requests\Concerns\DomainRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNoteRequest extends FormRequest
{
    use DomainRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seance_id' => ['required', 'integer', 'exists:seances,id'],
            'stagiaire_cef' => ['required', 'string', 'max:32', 'exists:stagiaires,cef'],
            'valeur' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'type' => $this->noteTypeRules(true),
            'decision' => ['nullable', 'string', 'max:64'],
            'unique_triplet' => [
                Rule::unique('notes', 'id')->where(fn ($q) => $q
                    ->where('seance_id', $this->input('seance_id'))
                    ->where('stagiaire_cef', $this->input('stagiaire_cef'))
                    ->where('type', $this->input('type'))
                ),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['unique_triplet' => 'notes']);
    }
}

