<?php

namespace App\Http\Requests\DateSeance;

use Illuminate\Foundation\Http\FormRequest;

class StoreDateSeanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seance_id' => ['required', 'integer', 'exists:seances,id', 'unique:date_seances,seance_id'],
            'date' => ['required', 'date'],
        ];
    }
}

