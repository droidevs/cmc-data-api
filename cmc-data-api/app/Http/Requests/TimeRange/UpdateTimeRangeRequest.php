<?php

namespace App\Http\Requests\TimeRange;

use Illuminate\Foundation\Http\FormRequest;

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

        return [
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time'   => [
                'sometimes',
                'date_format:H:i',
                function (string $attribute, mixed $value, \Closure $fail) use ($timeRange) {
                    $start = $this->input('start_time', $timeRange?->start_time?->format('H:i'));

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
            'end_time.date_format'   => 'L\'heure de fin doit être au format HH:MM.',
        ];
    }
}
