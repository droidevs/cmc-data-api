<?php

namespace App\Http\Requests\TimeRange;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTimeRangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $timeRangeId = $this->route('time_range')?->getKey() ?? $this->route('time_range');

        return [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'unique_pair' => [
                Rule::unique('time_ranges', 'id')
                    ->where(fn ($q) => $q
                        ->where('start_time', $this->input('start_time'))
                        ->where('end_time', $this->input('end_time'))
                    )
                    ->ignore($timeRangeId),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['unique_pair' => 'time_ranges']);
    }
}

