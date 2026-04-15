<?php

namespace App\Http\Requests\Pole;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle' => ['required', 'string', 'max:255'],
        ];
    }
}

