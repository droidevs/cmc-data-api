<?php

namespace App\Http\Requests\TimeRange;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for updating an existing TimeRange (PATCH semantics).
 * All fields optional — only send what you want to change.
 */
class UpdateTimeRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $timeRange = $this->route('time_range');
        $startTime = $this->input('start_time', $timeRange?->start_time);
        $endTime   = $this->input('end_time', $timeRange?->end_time);

        return [
            'start_time' => [
                'sometimes',
                'date_format:H:i',
                Rule::unique('time_ranges')
                    ->where('start_time', $startTime)
                    ->where('end_time', $endTime)
                    ->ignore($timeRange?->id),
            ],
            'end_time'   => [
                'sometimes',
                'date_format:H:i',
                Rule::unique('time_ranges')
                    ->where('start_time', $startTime)
                    ->where('end_time', $endTime)
                    ->ignore($timeRange?->id),
                function (string $attribute, mixed $value, \Closure $fail) use ($timeRange) {
                    $start = $this->input('start_time', $timeRange?->start_time ? substr((string) $timeRange->start_time, 0, 5) : null);

                    if ($start && $value <= $start) {
                        $fail('L\'heure de fin doit être après l\'heure de début.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.date_format' => 'L\'heure de début doit être au format HH:MM.',
            'start_time.unique'      => 'Ce créneau horaire (heure de début et fin) existe déjà.',
            'end_time.date_format'   => 'L\'heure de fin doit être au format HH:MM.',
            'end_time.unique'        => 'Ce créneau horaire (heure de début et fin) existe déjà.',
        ];
    }
}
