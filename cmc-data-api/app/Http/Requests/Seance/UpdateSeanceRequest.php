<?php

namespace App\Http\Requests\Seance;

use App\Http\Requests\Concerns\DomainRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSeanceRequest extends FormRequest
{
    use DomainRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'affectation_id' => ['required', 'integer', 'exists:affectations,id'],
            'type' => $this->seanceTypeRules(false),
        ];
    }
}

