<?php

namespace App\Http\Requests\TimeRange;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for creating a new TimeRange (scheduling slot).
 *
 * Matches TimeRange's `datetime:H:i` cast format, e.g. "08:30", "14:00".
 */
class StoreTimeRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required'    => 'L\'heure de début est obligatoire.',
            'start_time.date_format' => 'L\'heure de début doit être au format HH:MM.',
            'end_time.required'      => 'L\'heure de fin est obligatoire.',
            'end_time.date_format'   => 'L\'heure de fin doit être au format HH:MM.',
            'end_time.after'         => 'L\'heure de fin doit être après l\'heure de début.',
        ];
    }
}
