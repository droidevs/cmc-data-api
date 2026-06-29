<?php

namespace App\Http\Requests\TimeRange;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $startTime = $this->input('start_time');
        $endTime   = $this->input('end_time');

        return [
            'start_time' => [
                'required',
                'date_format:H:i',
                Rule::unique('time_ranges')
                    ->where('start_time', $startTime)
                    ->where('end_time', $endTime),
            ],
            'end_time'   => [
                'required',
                'date_format:H:i',
                'after:start_time',
                Rule::unique('time_ranges')
                    ->where('start_time', $startTime)
                    ->where('end_time', $endTime),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required'    => 'L\'heure de début est obligatoire.',
            'start_time.date_format' => 'L\'heure de début doit être au format HH:MM.',
            'start_time.unique'      => 'Ce créneau horaire (heure de début et fin) existe déjà.',
            'end_time.required'      => 'L\'heure de fin est obligatoire.',
            'end_time.date_format'   => 'L\'heure de fin doit être au format HH:MM.',
            'end_time.after'         => 'L\'heure de fin doit être après l\'heure de début.',
            'end_time.unique'        => 'Ce créneau horaire (heure de début et fin) existe déjà.',
        ];
    }
}
