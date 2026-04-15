<?php

namespace App\Http\Requests\DateSeance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDateSeanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $dateSeanceId = $this->route('date_seance')?->getKey() ?? $this->route('date_seance');

        return [
            'seance_id' => [
                'required',
                'integer',
                'exists:seances,id',
                Rule::unique('date_seances', 'seance_id')->ignore($dateSeanceId),
            ],
            'date' => ['required', 'date'],
        ];
    }
}

