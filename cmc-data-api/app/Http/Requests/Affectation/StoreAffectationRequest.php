<?php

namespace App\Http\Requests\Affectation;

use App\Http\Requests\Concerns\DomainRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAffectationRequest extends FormRequest
{
    use DomainRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'groupe_id' => ['required', 'integer', 'exists:groupes,id'],
            'module_code' => ['required', 'string', 'max:32', 'exists:modules,code_module'],
            'formateur_mle' => ['required', 'string', 'max:32', 'exists:formateurs,mle'],
            'mode' => $this->modeRules(false),
            'mh_affecte' => ['nullable', 'numeric', 'min:0'],
            // Composite unique (groupe_id, module_code, formateur_mle)
            'unique_triplet' => [
                Rule::unique('affectations', 'id')->where(fn ($q) => $q
                    ->where('groupe_id', $this->input('groupe_id'))
                    ->where('module_code', $this->input('module_code'))
                    ->where('formateur_mle', $this->input('formateur_mle'))
                ),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Trick to validate composite unique using a dummy attribute.
        $this->merge(['unique_triplet' => 'affectations']);
    }
}

